import Plugin from 'src/script/plugin-system/plugin.class';
import FormSerializeUtil from 'src/script/utility/form/form-serialize.util';
import DomAccess from 'src/script/helper/dom-access.helper';
import HttpClient from 'src/script/service/http-client.service';
import Te from '../template-engine';
import L from '../../node_modules/leaflet/dist/leaflet-src.js';

export default class MoorlMerchantFinder extends Plugin {

    static options = {};

    init() {

        this._form = this.el;

        this._client = new HttpClient(window.accessKey, window.contextToken);

        this._results = document.getElementById('searchResults');

        this._loadingIndicator = document.getElementById('loadingIndicator');

        this._popupTemplate = document.getElementById('embedMap').innerHTML;

        this._popupElement = document.createElement('div');

        document.getElementById('embedMap').innerHTML = '';

        this._resultTemplate = this._results.innerHTML;

        this._buildMap();

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
        $(document).on('click','[data-item]', function () {
            that._focusItem($(this).data('item'));
        });
    }

    _formSubmit(event) {
        if (typeof event != 'undefined') {
            event.preventDefault();
        }
        const requestUrl = DomAccess.getAttribute(this._form, 'action').toLowerCase();
        const formData = FormSerializeUtil.serialize(this._form);
        this._client.post(requestUrl, formData, this._onLoaded.bind(this))
    }

    _onLoaded(response) {
        response = JSON.parse(response);
        this._refresh();
        this._buildSearchResults(response);
        this._buildMapMarkers(response);
    }

    _buildSearchResults(response) {
        const that = this;
        const te = new Te();
        response.data.forEach(function (item) {
            that._results.insertAdjacentHTML('beforeend', te.render(that._resultTemplate, item));
        });
        $(this._results).removeClass('d-none');
        $(this._loadingIndicator).addClass('d-none');
    }

    _buildMapMarkers(response) {
        const that = this;
        const te = new Te();
        const featureMarker = [];
        let minLon = 10000,
            minLat = 10000,
            maxLon = 0,
            maxLat = 0;

        // add features
        response.data.forEach(function (item) {
            if (item.locationLon != null) {

                minLat = item.locationLat < minLat ? item.locationLat : minLat;
                maxLat = item.locationLat > maxLat ? item.locationLat : maxLat;
                minLon = item.locationLon < minLon ? item.locationLon : minLon;
                maxLon = item.locationLon > maxLon ? item.locationLon : maxLon;

                featureMarker.push(
                    L.marker([item.locationLat, item.locationLon], {data: item})
                        .bindPopup(te.render(that._popupTemplate, item), {
                            autoPan: false,
                            autoClose: false
                        })
                        .on('click', function() {that._focusItem(item.id)})
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

        if (response.data.length != 0) {

            if (response.data.length == 1) {
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

    }

    _focusItem(id) {
        const that = this;
        const myElement = document.getElementById(id);
        const topPos = myElement.offsetTop;

        document.getElementById('searchResults').scrollTo({
            top: topPos,
            behavior: 'smooth',
        });

        $('#searchResults li').removeClass('active');
        $('#' + id).addClass('active');

        this.ol.markers.eachLayer(function(layer) {

            if (layer.options.data.id == id) {
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
            }

        });
    }

    _buildMap() {
        this.ol = {};
        this.ol.markers = L.layerGroup([]);
        this.ol.map = L.map('embedMap', {});
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar', attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'}).addTo(this.ol.map);
    }

}
