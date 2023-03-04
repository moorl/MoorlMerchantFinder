import Plugin from 'src/plugin-system/plugin.class';
import FormSerializeUtil from 'src/utility/form/form-serialize.util';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';
import L from 'leaflet';

/**
 * @deprecated: will be removed
 */
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

        this._options = {
            'foo': 'bar'
        };
        this._reponse = null
        this._isFocusing = false;

        this._searchParams = this.el.dataset.searchParams;
        this._registerEvents();
        this._formSubmit();
    }

    _refresh() {
        this._results.innerHTML = '';
    }

    _registerEvents() {
        const that = this;

        this._form.addEventListener('submit', this._formSubmit.bind(this));

        /*if (this.ol && this.ol.map) {
            const map = this.ol.map;

            map.on('click', function () {
                if (map.scrollWheelZoom.enabled()) {
                    map.scrollWheelZoom.disable();
                } else {
                    map.scrollWheelZoom.enable();
                }
            });
        }*/

        $(this.el).on('change', 'input[type=checkbox]', function () {
            const button = that._form.querySelector("[type=submit]");
            button.click();
        });

        $(this.el).on('click', '[data-moorl-foundation-modal]', function () {
            const url = this.dataset.moorlFoundationModal;
            window.moorlFoundationModal(url);
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
        });

        $(this.el).on('click', '[data-trigger]', function () {
            const button = that.el.querySelector('[data-bs-toggle=modal]');

            button.dataset.url = this.dataset.url;
            button.click();
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
            if (that._form.querySelector('[name=' + name + ']')) {
                that._form.querySelector('[name=' + name + ']').value = value;
            }
        });
    }

    _getItem(id) {
        return this._reponse.data.find(o => o.id === id);
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
            });
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    }

    _formSubmit(event) {
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
            formData.set("action", event.submitter.value);

            if (event.submitter.dataset.merchant) {
                formData.set("merchant", event.submitter.dataset.merchant);
            }
        }

        const button = this._form.querySelector("[type=submit]");
        button.disabled = true;

        this._client.post(requestUrl, formData, this._onLoaded.bind(this))
    }

    _onLoaded(response) {
        const button = this._form.querySelector("[type=submit]");
        button.disabled = false;

        this._reponse = JSON.parse(response);

        if (this._reponse.reload) {
            location.reload();
        }

        if (this._reponse.data) {
            this._refresh();
            this._buildSearchResults();

            if (this._mapElement) {
                this.ol.map.invalidateSize();
                this._buildMapMarkers();
            }
        }
    }

    _buildSearchResults() {
        if (this._resultInfo) {
            this._resultInfo.innerHTML = this._reponse.searchInfo;
        }

        if (typeof this._reponse.html != 'undefined') {
            this._results.innerHTML = this._reponse.html;
        }

        $(this._results).removeClass('d-none');
        $(this._loadingIndicator).addClass('d-none');
    }

    _getIcon(icon) {
        if (icon.svg) {
            const size = 40;
            const iconOptions = {
                iconSize: [size, size + size / 2],
                iconAnchor: [size/2, size + size / 2],
                popupAnchor: [0, -size],
                className: icon.className,
                html: `<div class="marker-pin"></div>${icon.svg}`
            }
            return L.divIcon(iconOptions);
        } else if (icon.marker) {
            let iconOptions = {};

            if (icon.markerSettings != null) {
                const ms = icon.markerSettings;

                iconOptions.iconSize = [ms.iconSizeX, ms.iconSizeY];
                iconOptions.shadowSize = [ms.shadowSizeX, ms.shadowSizeY];
                iconOptions.iconAnchor = [ms.iconAnchorX, ms.iconAnchorY];
                iconOptions.shadowAnchor = [ms.shadowAnchorX, ms.shadowAnchorY];
                iconOptions.popupAnchor = [ms.popupAnchorX, ms.popupAnchorY];
            }

            if (icon.markerShadow) {
                iconOptions.shadowUrl = icon.markerShadow.url;
            }

            if (icon.markerRetina) {
                iconOptions.iconRetinaUrl = icon.markerRetina.url;
            }

            if (icon.marker) {
                iconOptions.iconUrl = icon.marker.url;
            }

            return L.icon(iconOptions);
        }

        return null;
    }

    _buildMapMarkers() {
        const that = this;
        const featureMarker = [];

        // add features
        this._reponse.data.forEach(function (item) {
            if (item.locationLon != null) {
                let markerOptions = { data: item };

                if (item.marker != null) {
                    markerOptions.icon = that._getIcon(item.marker);
                }

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
                            if (!that._isFocusing) {
                                that.ol.map.fitBounds(that.ol.markers.getBounds(), {padding: [1, 1]});
                            }
                        })
                );
            }
        });

        this.ol.markers.clearLayers();

        if (featureMarker.length === 0) {
            return;
        }

        this.ol.markers = L.featureGroup(featureMarker).addTo(that.ol.map);
        this.ol.map.fitBounds(this.ol.markers.getBounds(), {padding: [1, 1]});
    }

    _updateMerchantId(id) {
        $('.input-moorl-merchant-id').val(id);
        const item = this._getItem(id);
        $('.label-moorl-merchant-id').html(item.popup);
    }

    _focusItem(id) {
        this._isFocusing = true;

        this._updateMerchantId(id);

        const that = this;
        const myElement = this.el.getElementsByClassName('merchant-' + id)[0];

        if (myElement === false) {
            return;
        }

        const topPos = myElement.offsetTop;

        this._resultsContent.scrollTo({
            top: topPos,
            behavior: 'smooth',
        });

        $(this.el).find('.moorl-merchant-finder-results li').removeClass('active');
        $(this.el).find('.merchant-' + id).addClass('active');

        this.ol.markers.eachLayer(function (layer) {
            if (layer.options.data.id === id) {
                let position = layer.getLatLng();
                if (!layer.getPopup().isOpen()) {
                    layer.openPopup();
                }
                that.ol.map.flyTo(position, 16, {animate: true, duration: 1});
                that._isFocusing = false;
            }
        });
    }

    _buildMap() {
        this.ol = {};
        this.ol.markers = L.layerGroup([]);
        this.ol.map = L.map(this._mapElement, {
            scrollWheelZoom: false
        });
        L.tileLayer(this.el.dataset.tileLayerUrl, {attribution: this.el.dataset.tileLayerCopy}).addTo(this.ol.map);
    }
}
