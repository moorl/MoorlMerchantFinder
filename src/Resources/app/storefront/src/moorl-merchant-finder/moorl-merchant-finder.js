import Plugin from 'src/plugin-system/plugin.class';
import FormSerializeUtil from 'src/utility/form/form-serialize.util';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';

import Te from '../template-engine';

import {fromLonLat, transformExtent} from 'ol/proj.js';
import {Feature, Map, View} from 'ol';
import {Tile, Vector} from 'ol/layer';
import {Vector as VectorSource} from 'ol/source';
import {Icon, Style} from 'ol/style';
import {Point} from 'ol/geom';
//import {defaults as defaultInteractions, Pointer as PointerInteraction} from '../../node_modules/ol/interaction';
import OSM from 'ol/source/OSM';
import Overlay from 'ol/Overlay.js';

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

        this.ol.map.on('click', function(evt) {
            var feature = that.ol.map.forEachFeatureAtPixel(evt.pixel,
                function(feature) {
                    return feature;
                });
            if (feature) {
                const item = feature.get('item');
                that._focusItem(item.id);
            } else {
                that._unFocusItem();
            }
        });

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

        if (this.ol.markerVectorLayer) {
            // remove existing markers
            this.ol.map.removeLayer(this.ol.markerVectorLayer);
        }

        const featureMarker = [];
        const styleMarker = new Style({
            image: new Icon({
                scale: .7, anchor: [0.5, 1],
                src: 'https://cdn.mapmarker.io/api/v1/fa?size=70&icon=fa-pin&color=%23D33115&',
            }),
        });
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

                featureMarker.push(new Feature({
                    geometry: new Point(fromLonLat([
                        item.locationLon,
                        item.locationLat,
                    ])),
                    item: item,
                }));
            }
        });

        if (response.data.length != 0) {

            if (response.data.length == 1) {

                minLat = minLat - 0.02;
                maxLat = maxLat + 0.02;
                minLon = minLon - 0.02;
                maxLon = maxLon + 0.02;

            }

            this.ol.markers = new VectorSource({features: featureMarker});
            this.ol.markerVectorLayer = new Vector({source: this.ol.markers, style: [styleMarker]});
            this.ol.map.addLayer(this.ol.markerVectorLayer);

            // relocate bounding box
            this.ol.view.fit(transformExtent([minLon, minLat, maxLon, maxLat], 'EPSG:4326', this.ol.view.getProjection()), {
                size: this.ol.map.getSize(),
                duration: 500,
            });

        }

        this.ol.lastView = null;

    }

    _focusItem(id) {

        const te = new Te();

        const myElement = document.getElementById(id);
        const topPos = myElement.offsetTop;

        document.getElementById('searchResults').scrollTo({
            top: topPos,
            behavior: 'smooth',
        });

        $('#searchResults li').removeClass('active');
        $('#' + id).addClass('active');

        const feature = this.ol.markers.forEachFeature(
            function(feature) {
                const item = feature.get('item');
                if (item.id == id) {
                    return feature;
                }
            });
        if (feature) {
            const coordinates = feature.getGeometry().getCoordinates();
            const item = feature.get('item');
            this._popupElement.innerHTML = te.render(this._popupTemplate, item);
            this.ol.popup.setPosition(coordinates);

            if (this.ol.lastView == null) {
                this.ol.lastView = {
                    center: this.ol.view.getCenter(),
                    zoom: this.ol.view.getZoom(),
                    duration: 500,
                };
            }

            this.ol.view.animate({
                center: coordinates,
                duration: 500,
                zoom: 16,
            });
        }

    }

    _unFocusItem() {
        $('#searchResults li').removeClass('bg-light');
        this._popupElement.innerHTML = '';
        this.ol.view.animate(this.ol.lastView);
        this.ol.lastView = null;
    }

    _buildMap() {

        this.ol = {};

        // TODO: Auto position by search results
        this.ol.loc = [
            10.018343,
            51.133481,
        ];

        this.ol.pos = fromLonLat(this.ol.loc);

        this.ol.lastView = null;

        this.ol.view = new View({
            center: this.ol.pos,
            zoom: 6,
        });

        this.ol.map = new Map({
            target: 'embedMap',
            layers: [
                new Tile({source: new OSM()}),
            ],
            view: this.ol.view,
        });

        this.ol.popup = new Overlay({
            element: this._popupElement,
            positioning: 'bottom-center',
            stopEvent: false,
            offset: [0, -45],
        });
        this.ol.map.addOverlay(this.ol.popup);
    }

}
