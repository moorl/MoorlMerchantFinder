import {Component, Mixin, Application, State} from 'src/core/shopware';
import utils from 'src/core/service/util.service';
import Criteria from 'src/core/data-new/criteria.data';
import template from './moorl-merchant-finder-detail.html.twig';

import {fromLonLat, toLonLat} from 'ol/proj.js';
import {Map, Overlay, View, Feature} from 'ol';
import {Tile, Vector} from 'ol/layer';
import {Vector as VectorSource} from 'ol/source';
import {Style, Icon} from 'ol/style';
import {Point} from 'ol/geom';
import {defaults as defaultInteractions, Pointer as PointerInteraction} from 'ol/interaction';
import OSM from 'ol/source/OSM';

var Drag = (function (PointerInteraction) {
    function Drag() {
        PointerInteraction.call(this, {
            handleDownEvent: handleDownEvent,
            handleDragEvent: handleDragEvent,
            handleMoveEvent: handleMoveEvent,
            handleUpEvent: handleUpEvent
        });
        this.coordinate_ = null;
        this.cursor_ = 'pointer';
        this.feature_ = null;
        this.previousCursor_ = undefined;
    }

    if ( PointerInteraction ) Drag.__proto__ = PointerInteraction;
    Drag.prototype = Object.create( PointerInteraction && PointerInteraction.prototype );
    Drag.prototype.constructor = Drag;

    return Drag;
}(PointerInteraction));

function handleDownEvent(evt) {
    var map = evt.map;
    var feature = map.forEachFeatureAtPixel(evt.pixel,
        function(feature) {
            return feature;
        });
    if (feature) {
        this.coordinate_ = evt.coordinate;
        this.feature_ = feature;
    }
    return !!feature;
}

function handleDragEvent(evt) {
    var deltaX = evt.coordinate[0] - this.coordinate_[0];
    var deltaY = evt.coordinate[1] - this.coordinate_[1];
    var geometry = this.feature_.getGeometry();
    geometry.translate(deltaX, deltaY);
    this.coordinate_[0] = evt.coordinate[0];
    this.coordinate_[1] = evt.coordinate[1];
}

function handleMoveEvent(evt) {
    if (this.cursor_) {
        var map = evt.map;
        var feature = map.forEachFeatureAtPixel(evt.pixel,
            function(feature) {
                return feature;
            });
        var element = evt.map.getTargetElement();
        if (feature) {
            if (element.style.cursor != this.cursor_) {
                this.previousCursor_ = element.style.cursor;
                element.style.cursor = this.cursor_;
            }
        } else if (this.previousCursor_ !== undefined) {
            element.style.cursor = this.previousCursor_;
            this.previousCursor_ = undefined;
        }
    }
}

function handleUpEvent() {
    this.coordinate_ = null;
    this.feature_ = null;
    return false;
}

Component.register('moorl-merchant-finder-detail', {
    template,

    inject: [
        'repositoryFactory',
        'context'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder'),
        Mixin.getByName('discard-detail-page-changes')('merchant')
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onAbortButtonClick'
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            merchant: null,
            salesChannels: null,
            countries: null,
            customerGroups: null,
            isLoading: true,
            processSuccess: false,
            repository: null,
            searchTerm: '',
            mediaEntity: null,
            showPicker: false,
            showUploadField: false,
            suggestedItems: [],
            isLoadingSuggestions: false,
            pickerClasses: {},
            uploadTag: utils.createId(),
            mediaItem: null,
            customFieldSets: [],
        };
    },

    computed: {
        mediaStore() {
            return State.getStore('media');
        },

        moorlMerchantRepository() {
            return this.repositoryFactory.create('moorl_merchant');
        },

        customerGroupRepository() {
            return this.repositoryFactory.create('customer_group');
        },

        countryRepository() {
            return this.repositoryFactory.create('country');
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },
    },

    created() {
        this.repository = this.moorlMerchantRepository;
        this.initializeFurtherComponents();
        this.getMerchant();
    },

    methods: {

        initializeFurtherComponents() {

            this.salesChannelRepository.search(new Criteria(1, 100), this.context).then((searchResult) => {
                this.salesChannels = searchResult;
            });

            this.customerGroupRepository.search(new Criteria(1, 100), this.context).then((searchResult) => {
                this.customerGroups = searchResult;
            });

            const countryCriteria = new Criteria(1, 100);
            countryCriteria.addSorting(Criteria.sort('name'));
            this.countryRepository.search(countryCriteria, this.context).then((searchResult) => {
                this.countries = searchResult;
            });

        },

        getMerchant() {
            this.repository
                .get(this.$route.params.id, this.context)
                .then((entity) => {
                    this.merchant = entity;
                    this.mediaItem = this.merchant.mediaId ? this.mediaStore.getById(this.merchant.mediaId) : null;
                    this.isLoading = false;
                });
        },

        drawMap() {

            if (typeof this.ol != 'undefined') {
                console.log(this.ol);
                return
            }

            var styleMarker = new Style({
                image: new Icon({
                    scale: .7, anchor: [0.5, 1],
                    src: 'https://cdn.mapmarker.io/api/v1/fa?size=70&icon=fa-pin&color=%23D33115&'
                })
            });

            this.ol = {};

            this.ol.loc = [
                this.merchant.locationLon,
                this.merchant.locationLat
            ];

            this.ol.pos = fromLonLat(this.ol.loc);

            this.ol.featureMarker = new Feature(
                new Point(this.ol.pos)
            );

            this.ol.view = new View({
                center: this.ol.pos,
                zoom: 16
            });

            this.ol.map = new Map({
                interactions: defaultInteractions().extend([new Drag()]),
                target: 'embedMap',
                layers: [
                    new Tile({source: new OSM()}),
                    new Vector({
                        source: new VectorSource({
                            features: [this.ol.featureMarker]
                        }),
                        style: [styleMarker],
                    })
                ],
                view: this.ol.view
            });

        },

        getPositionByAddress() {
            this.isLoading = true;
            const initContainer = Application.getContainer('init');
            const httpClient = initContainer.httpClient;
            const searchParams = new URLSearchParams({
                "format": "json",
                "zipcode": this.merchant.zipcode,
                "city": this.merchant.city,
                "street": this.merchant.street + " " + this.merchant.streetNumber,
                "country": this.merchant.countryCode
            });
            httpClient.get(`http://nominatim.openstreetmap.org/search?` + searchParams).then((response) => {

                console.log(response);

                this.ol.loc = [
                    parseFloat(response.data[0].lon),
                    parseFloat(response.data[0].lat),
                ];

                this.ol.pos = fromLonLat(this.ol.loc);

                this.ol.featureMarker.getGeometry().setCoordinates(this.ol.pos);

                this.ol.view.animate({
                    center: this.ol.pos,
                    duration: 500,
                    zoom: 16
                });

                this.isLoading = false;

            }).catch((exception) => {
                console.log(exception);
                this.isLoading = false;
                throw exception;
            });
        },

        posSelect() {
            this.ol.loc = toLonLat(this.ol.featureMarker.getGeometry().getCoordinates());
            console.log("Koordinaten", this.ol.loc);
            this.merchant.locationLon = this.ol.loc[0];
            this.merchant.locationLat = this.ol.loc[1];
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.merchant, this.context)
                .then(() => {
                    this.getMerchant();
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-merchant-finder.detail.errorTitle'),
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        },

        setMediaItem({ targetId }) {
            this.merchant.mediaId = targetId;
            this.mediaStore.getByIdAsync(targetId);
        },

        onDropMedia(dragData) {
            this.setMediaItem({ targetId: dragData.id });
        },

        setMediaFromSidebar(mediaEntity) {
            this.merchant.mediaId = mediaEntity.id;
        },

        onUnlinkLogo() {
            this.merchant.mediaId = null;
        },

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        }
    }

});
