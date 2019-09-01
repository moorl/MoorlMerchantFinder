import {Component, Mixin, Application, State} from 'src/core/shopware';
import utils from 'src/core/service/util.service';
import Criteria from 'src/core/data-new/criteria.data';
import template from './moorl-merchant-finder-detail.html.twig';
import L from 'leaflet';

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
            markerItem: null,
            markerShadowItem: null,
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
                    this.markerItem = this.merchant.markerId ? this.mediaStore.getById(this.merchant.markerId) : null;
                    this.markerShadowItem = this.merchant.markerShadowId ? this.mediaStore.getById(this.merchant.markerShadowId) : null;
                    this.isLoading = false;
                });
        },

        drawMap() {
            const that = this;
            this.ol = {};
            this.ol.center = [
                this.merchant.locationLat,
                this.merchant.locationLon
            ];
            this.ol.map = L.map('embedMap', {
                center: this.ol.center,
                zoom: 16
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar', attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'}).addTo(this.ol.map);
            this.ol.marker = L.marker(this.ol.center, {draggable: true})
                .on('dragend',function () {
                    that.ol.center = that.ol.marker.getLatLng();
                    that.ol.map.flyTo(that.ol.center, 16, {animate: true, duration: 1});
                })
                .addTo(this.ol.map);
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
                this.ol.center = [
                    parseFloat(response.data[0].lat),
                    parseFloat(response.data[0].lon),
                ];
                this.ol.map.flyTo(this.ol.center, 16, {animate: true, duration: 1});
                this.ol.marker.setLatLng(this.ol.center);
                this.isLoading = false;
            }).catch((exception) => {
                console.log(exception);
                this.isLoading = false;
                throw exception;
            });
        },

        posSelect() {
            console.log(this.ol.center);
            this.merchant.locationLat = this.ol.center.lat;
            this.merchant.locationLon = this.ol.center.lng;
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

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },

        // Logo
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
        onUnlinkMedia() {
            this.merchant.mediaId = null;
        },

        // Marker
        setMarkerItem({ targetId }) {
            this.merchant.markerId = targetId;
            this.mediaStore.getByIdAsync(targetId);
        },
        onDropMarker(dragData) {
            this.setMarkerItem({ targetId: dragData.id });
        },
        setMarkerFromSidebar(mediaEntity) {
            this.merchant.markerId = mediaEntity.id;
        },
        onUnlinkMarker() {
            this.merchant.markerId = null;
        },

        // Marker Shadow
        setMarkerShadowItem({ targetId }) {
            this.merchant.markerShadowId = targetId;
            this.mediaStore.getByIdAsync(targetId);
        },
        onDropMarkerShadow(dragData) {
            this.setMarkerShadowItem({ targetId: dragData.id });
        },
        setMarkerShadowFromSidebar(mediaEntity) {
            this.merchant.markerShadowId = mediaEntity.id;
        },
        onUnlinkMarkerShadow() {
            this.merchant.markerShadowId = null;
        }

    }

});
