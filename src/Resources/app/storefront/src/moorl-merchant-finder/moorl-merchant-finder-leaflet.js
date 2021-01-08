import Plugin from 'src/plugin-system/plugin.class';
import FormSerializeUtil from 'src/utility/form/form-serialize.util';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';
import Te from '../template-engine'; // Remove 1.3
import L from 'leaflet';
import 'url-search-params-polyfill';

export default class MoorlMerchantFinder extends Plugin {

    static options = {};

    init() {
        //const that = this;
        this._form = this.el.getElementsByTagName("form")[0];

        if (!this._form) {
            // prevent errors if CSS selector is just used for styling
            return;
        }

        this._client = new HttpClient(window.accessKey, window.contextToken);
        this._results = this.el.getElementsByClassName('moorl-merchant-finder-results')[0];
        this._resultsContent = this.el.getElementsByClassName('results-content')[0];
        this._loadingIndicator = this.el.getElementsByClassName('moorl-merchant-finder-loading')[0];
        this._resultTemplate = this._results.innerHTML;
        this._resultInfo = this.el.getElementsByClassName('moorl-merchant-finder-result-info')[0];
        this._mapElement = this.el.getElementsByClassName('moorl-merchant-finder-map')[0];

        if (this._mapElement) {
            this._popupTemplate = this._mapElement.innerHTML;
            this._popupElement = document.createElement('div');
            this._mapElement.innerHTML = '';
            this._buildMap();
            this._resizeMap = true;
        }

        if (this.el.dataset.defaultMarker) {
            this._defaultMarker = JSON.parse(this.el.dataset.defaultMarker);
        } else {
            this._defaultMarker = {};
        }

        this._options = {
            'foo': 'bar'
        };

        this._searchParams = this.el.dataset.searchParams;
        this._registerEvents();
        this._formSubmit();
    }

    _refresh() {
        // Remove Search Results
        this._results.innerHTML = '';
    }

    _registerEvents() {
        const that = this;

        this.el.addEventListener('submit', this._formSubmit.bind(this));

        $(this.el).on('change', 'input[type=checkbox]', function () {
            const button = that._form.querySelector("[type=submit]");
            button.click();
        });

        $(this.el).on('change', 'select', function () {
            const button = that._form.querySelector("[type=submit]");
            button.click();
        });

        $(this.el).on('click', 'button[name=location]', function () {
            $(this).toggleClass('active');
            if ($(this).hasClass('active')) {
                that._getLocation();
                $(that.el).find('input[name=zipcode]').prop('disabled', true);
            } else {
                that._options.myLocation = null;
                $(that.el).find('input[name=zipcode]').prop('disabled', false).focus();
            }
        });

        $(this.el).on('click', '[data-item]', function () {
            that._focusItem(this.dataset.item);
            that._updateMerchantStockSelection(this.dataset.item);
        });

        $(this.el).on('click', '[data-trigger]', function () {
            const button = that.el.querySelector('[data-toggle=modal]');

            button.dataset.url = this.dataset.url;
            button.click();
            console.log(this);
        });

        $(this.el).on('click', 'button[data-merchant]', function () {
            const button = that._form.querySelector("[type=submit]");

            button.dataset.merchant = this.dataset.merchant;
            button.value = 'pick';
            button.click();
        });

        $(window).on('shown.bs.modal', function() {
            if (that._mapElement && that._resizeMap) {
                // Reload because the map has problems with the modal
                that._formSubmit();
                that._resizeMap = false;
            }
        });

        const urlParams = new URLSearchParams(window.location.search);

        urlParams.forEach(function (value, name) {
            //console.log(name, value);
            if (that._form.querySelector('[name=' + name + ']')) {
                that._form.querySelector('[name=' + name + ']').value = value;
            }
        });
    }

    _getLocation() {
        const that = this;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                that._options.myLocation = [
                    {
                        'lat': position.coords.latitude,
                        'lon': position.coords.longitude
                    }
                ];
                console.log(that._options);
            });
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    }

    _formSubmit(event) {
        console.log(event);

        if (typeof event != 'undefined') {
            event.preventDefault();
        }

        const requestUrl = DomAccess.getAttribute(this._form, 'action').toLowerCase();
        const formData = FormSerializeUtil.serialize(this._form);

        if (this._searchParams) {
            const queryString = new URLSearchParams(formData);
            queryString.delete("_csrf_token");
            queryString.delete("items");
            queryString.delete("rules");
            queryString.delete("rules[]");

            if (history.pushState) {
                let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + queryString.toString();
                window.history.pushState({path: newUrl}, '', newUrl);
            }
        }

        formData.set("options", JSON.stringify(this._options));

        if (event && event.submitter && event.submitter.value) {
            //console.log(formData);
            formData.set("action", event.submitter.value);

            if (event.submitter.dataset.merchant) {
                formData.set("merchant", event.submitter.dataset.merchant);
            }
        }

        this._client.post(requestUrl, formData, this._onLoaded.bind(this))
    }

    _onLoaded(response) {
        response = JSON.parse(response);

        if (response.reload) {
            location.reload();
        }

        if (response.data) {
            this._refresh();
            this._buildSearchResults(response);

            if (this._mapElement) {
                this.ol.map.invalidateSize();
                this._buildMapMarkers(response);
            }
        }
    }

    _buildSearchResults(response) {
        const that = this;
        const te = new Te();
        if (this._resultInfo) {
            this._resultInfo.innerHTML = response.searchInfo;
        }

        if (typeof response.html != 'undefined') {
            this._results.innerHTML = response.html;
        } else {
            response.data.forEach(function (item) {
                that._results.insertAdjacentHTML('beforeend', te.render(that._resultTemplate, item));
            });
        }

        $(this._results).removeClass('d-none');
        $(this._loadingIndicator).addClass('d-none');
    }

    _buildMapMarkers(response) {
        const that = this;
        const featureMarker = [];
        let minLon = 10000,
            minLat = 10000,
            maxLon = 0,
            maxLat = 0;

        // add features
        response.data.forEach(function (item) {
            if (item.locationLon != null) {
                let iconOptions = that._defaultMarker;
                let markerOptions = {data: item};

                if (item.markerSettings != null) {
                    iconOptions = JSON.parse(item.markerSettings);
                }

                if (item.markerShadow) {
                    iconOptions.shadowUrl = item.markerShadow.url;
                }

                if (item.marker) {
                    iconOptions.iconUrl = item.marker.url;
                }

                if (iconOptions) {
                    markerOptions.icon = L.icon(iconOptions);
                }

                minLat = item.locationLat < minLat ? item.locationLat : minLat;
                maxLat = item.locationLat > maxLat ? item.locationLat : maxLat;
                minLon = item.locationLon < minLon ? item.locationLon : minLon;
                maxLon = item.locationLon > maxLon ? item.locationLon : maxLon;

                //console.log(item.popup);

                featureMarker.push(
                    L.marker([item.locationLat, item.locationLon], markerOptions)
                        .bindPopup(item.popup, {
                            autoPan: false,
                            autoClose: true
                        })
                        .on('click', function () {
                            that._focusItem(item.id)
                        })
                        .on('popupclose', function () {
                            if (that.ol.center) {
                                that.ol.map.flyTo(that.ol.center, that.ol.zoom, {animate: true, duration: 1});
                                that.ol.center = that.ol.zoom = null;
                            }
                        })
                );
            }
        });

        this.ol.markers.clearLayers();
        this.ol.markers = L.layerGroup(featureMarker).addTo(that.ol.map);

        if (response.data.length == 1) {
            minLat = minLat - 0.02;
            maxLat = maxLat + 0.02;
            minLon = minLon - 0.02;
            maxLon = maxLon + 0.02;
        } else if (response.data.length == 0 && response.myLocation && response.myLocation.length > 0) {
            minLon = maxLon = response.myLocation[0].lon;
            minLat = maxLat = response.myLocation[0].lat;
            minLat = minLat - 0.02;
            maxLat = maxLat + 0.02;
            minLon = minLon - 0.02;
            maxLon = maxLon + 0.02;
        }

        // relocate bounding box
        this.ol.map.fitBounds([
            [minLat, minLon],
            [maxLat, maxLon]
        ])
    }

    _updateMerchantStockSelection(id) {
        const merchantStockModalValue = document.getElementById("merchantStockModalValue");
        if (merchantStockModalValue !== false) {
            merchantStockModalValue.value = id;
        }

        const merchantStockModalLabel = document.getElementById("merchantStockModalLabel");
        if (merchantStockModalLabel === false) {
            return;
        }

        this.ol.markers.eachLayer(function (layer) {
            if (layer.options.data.id === id) {
                merchantStockModalLabel.innerHTML = layer.getPopup()._content;
            }
        });
    }

    _focusItem(id) {
        const that = this;
        const myElement = document.getElementById(id);
        if (myElement === false) {
            return;
        }

        const topPos = myElement.offsetTop;

        this._resultsContent.scrollTo({
            top: topPos,
            behavior: 'smooth',
        });

        $('.moorl-merchant-finder-results li').removeClass('active');
        $('#' + id).addClass('active');

        this.ol.markers.eachLayer(function (layer) {
            if (layer.options.data.id === id) {
                let position = layer.getLatLng();
                if (!layer.getPopup().isOpen()) {
                    layer.openPopup();
                }
                if (that.ol.center == null) {
                    that.ol.center = that.ol.map.getCenter();
                    that.ol.zoom = that.ol.map.getZoom();
                }
                that.ol.map.flyTo(position, 16, {animate: true, duration: 1});
                console.log(layer);

                if (merchantStockModalLabel !== false) {
                    merchantStockModalLabel.innerHTML = layer.getPopup()._content;
                }
            }
        });
    }

    _buildMap() {
        this.ol = {};
        this.ol.markers = L.layerGroup([]);
        this.ol.map = L.map(this._mapElement, {});
        L.tileLayer(this.el.dataset.tileLayerUrl, {attribution: this.el.dataset.tileLayerCopy}).addTo(this.ol.map);
    }
}
