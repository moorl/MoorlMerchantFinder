const {Component, Mixin, Application} = Shopware;
const {Criteria, EntityCollection} = Shopware.Data;
const utils = Shopware.Utils;

import template from './index.html.twig';
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
            uploadTagMedia: utils.createId(),
            uploadTagMarker: utils.createId(),
            uploadTagMarkerShadow: utils.createId(),
            customFieldSets: [],
            manufacturers: null,
            manufacturerIds: [],
            openingHours: null,
            openingHour: null,
            showOpeningHourModal: false
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
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

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        openingHourRepo() {
            return this.repositoryFactory.create('moorl_merchant_oh');
        },

        openingHourColumns() {
            return [
                {
                    property: 'title',
                    label: this.$tc('moorl-foundation.properties.title'),
                    sortable: false
                }, {
                    property: 'date',
                    label: this.$tc('moorl-foundation.properties.date'),
                    sortable: true
                }, {
                    property: 'showFrom',
                    label: this.$tc('moorl-foundation.properties.showFrom'),
                    dataIndex: 'showFrom',
                    sortable: true
                }, {
                    property: 'showUntil',
                    label: this.$tc('moorl-foundation.properties.showUntil'),
                    dataIndex: 'showUntil',
                    sortable: false
                },
                {
                    property: 'repeat',
                    label: this.$tc('moorl-foundation.properties.repeat'),
                    sortable: false
                },
                {
                    property: 'merchantId',
                    label: this.$tc('moorl-foundation.properties.locked'),
                    sortable: false
                }
            ];
        },

        defaultCriteria() {
            const criteria = new Criteria();
            criteria
                .addAssociation('tags')
                .addAssociation('productManufacturers')
                .addAssociation('categories');

            return criteria;
        }
    },

    created() {
        this.repository = this.moorlMerchantRepository;
        this.initializeFurtherComponents();
        this.getMerchant();
    },

    mounted() {
        const that = this;
        setTimeout(function () {
            that.drawMap();
        }, 3000);
    },

    methods: {
        getBaseOpeningHours() {
            return [{from: '08:00', until: '12:00'}, {from: '14:00', until: '18:00'}]
        },

        getEmptyTimetable() {
            return [
                {info: 'Ruhetag', openingHours: []}, // Montag
                {info: null, openingHours: this.getBaseOpeningHours()}, // Dienstag
                {info: null, openingHours: this.getBaseOpeningHours()}, // Mittwoch
                {info: null, openingHours: this.getBaseOpeningHours()}, // Donnerstag
                {info: null, openingHours: this.getBaseOpeningHours()}, // Freitag
                {info: null, openingHours: []}, // Samstag
                {info: null, openingHours: []} // Sonntag
            ];
        },

        sanitizeTimetable(item) {
            console.log("sanitizeTimetable", item)

            if (item) {
                return item;
            }

            return this.getEmptyTimetable();
        },

        getOpeningHour(openingHourId) {
            console.log(openingHourId);

            if (openingHourId) {
                console.log("getOpeningHour with ID", openingHourId);

                this.openingHourRepo
                    .get(openingHourId, Shopware.Context.api)
                    .then((entity) => {
                        this.openingHour = entity;
                        this.openingHour.openingHours = this.sanitizeTimetable(this.openingHour.openingHours);
                        this.showOpeningHourModal = true;
                    });
            } else {
                console.log("getOpeningHour without ID");
                this.openingHour = this.openingHourRepo.create(Shopware.Context.api);
                this.openingHour.openingHours = this.sanitizeTimetable(this.openingHour.openingHours);
                this.showOpeningHourModal = true;
            }
        },

        deleteOpeningHour(openingHourId) {
            this.openingHourRepo
                .delete(openingHourId, Shopware.Context.api)
                .then(() => {
                    this.loadOpeningHours();
                });
        },

        removeTimes(index) {
            this.openingHour.openingHours[index].openingHours.pop();
        },

        addTimes(index) {
            this.openingHour.openingHours[index].openingHours.push({from: null, until: null});
        },

        loadOpeningHours() {
            let criteria = new Criteria(1, 100);
            criteria.addFilter(Criteria.multi('OR', [
                Criteria.equals('merchantId', this.merchant.id),
                Criteria.equals('merchantId', null)
            ]));
            criteria.addSorting(Criteria.sort('date'));

            return this.openingHourRepo.search(criteria, Shopware.Context.api).then((items) => {
                this.openingHours = items;
                this.isLoading = false;
            });
        },

        onOpeningHourSave() {
            this.isLoading = true;

            if (this.openingHour.merchantId) {
                this.openingHour.merchantId = this.merchant.id;
            }

            this.openingHourRepo
                .save(this.openingHour, Shopware.Context.api)
                .then(() => {
                    this.onCloseModal();
                    this.loadOpeningHours();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-foundation.general.saveError'),
                    message: exception
                });
            });
        },

        onCloseModal() {
            this.showOpeningHourModal = false;
            this.openingHour = null;
        },

        initializeFurtherComponents() {
            this.manufacturers = new EntityCollection('/product-manufacturer', 'product_manufacturer', Shopware.Context.api);

            this.salesChannelRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.salesChannels = searchResult;
            });

            this.customerGroupRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.customerGroups = searchResult;
            });

            this.categoryRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.categories = searchResult;
            });

            const countryCriteria = new Criteria(1, 100);
            countryCriteria.addSorting(Criteria.sort('name'));
            this.countryRepository.search(countryCriteria, Shopware.Context.api).then((searchResult) => {
                this.countries = searchResult;
            });
        },

        getMerchant() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api, this.defaultCriteria)
                .then((entity) => {
                    this.merchant = entity;
                    this.isLoading = false;
                    this.loadOpeningHours();
                });
        },

        onManufacturersChange() {
            this.merchant.manufacturers = this.manufacturers;
            this.manufacturerIds = this.manufacturers.getIds();
        },

        drawMap() {
            const that = this;
            this.ol = {};
            this.ol.center = [
                this.merchant.locationLat ? this.merchant.locationLat : 52.5173,
                this.merchant.locationLon ? this.merchant.locationLon : 13.4020,
            ];
            this.ol.map = L.map(this.$refs['embedMap'], {
                center: this.ol.center,
                zoom: this.merchant.locationLat ? 16 : 5
            });
            L.tileLayer('//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'}).addTo(this.ol.map);
            this.ol.marker = L.marker(this.ol.center, {draggable: true})
                .on('dragend', function () {
                    that.ol.center = that.ol.marker.getLatLng();
                    that.ol.map.setView(that.ol.center);

                    that.merchant.locationLat = that.ol.center.lat;
                    that.merchant.locationLon = that.ol.center.lng;
                })
                .addTo(this.ol.map);
        },

        getPositionByAddress() {
            //this.isLoading = true;
            const initContainer = Application.getContainer('init');
            const httpClient = initContainer.httpClient;
            let street = this.merchant.street;
            if (this.merchant.streetNumber !== null) {
                street += " " + this.merchant.streetNumber;
            }
            const searchParams = new URLSearchParams({
                "format": "json",
                "zipcode": this.merchant.zipcode,
                "city": this.merchant.city,
                "street": street,
                "country": this.merchant.countryCode
            });
            httpClient.get(`//nominatim.openstreetmap.org/search?` + searchParams).then((response) => {
                if (!response.data[0]) {
                    this.createNotificationError({
                        title: this.$t('moorl-foundation.notification.nominatimErrorTitle'),
                        message: this.$t('moorl-foundation.notification.nominatimErrorText', 0, this.merchant)
                    });
                } else {
                    if (this.ol) {
                        console.log(response);
                        this.ol.center = [
                            parseFloat(response.data[0].lat),
                            parseFloat(response.data[0].lon),
                        ];
                        this.ol.map.flyTo(this.ol.center, 16, {animate: true, duration: 1});
                        this.ol.marker.setLatLng(this.ol.center);
                    }
                    this.merchant.locationLat = parseFloat(response.data[0].lat);
                    this.merchant.locationLon = parseFloat(response.data[0].lon);
                    this.$forceUpdate();
                }
                //this.isLoading = false;
            }).catch((exception) => {
                // console.log(exception);
                //this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-foundation.detail.errorTitle'),
                    message: exception
                });
            });
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.merchant, Shopware.Context.api)
                .then(() => {
                    this.getMerchant();
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-foundation.detail.errorTitle'),
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
        setMediaItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.merchant.mediaId = targetId;
                this.merchant.media = updatedMedia;
            });
        },
        onDropMedia(dragData) {
            this.setMediaItem({targetId: dragData.id});
        },
        setMediaFromSidebar(mediaEntity) {
            this.merchant.mediaId = mediaEntity.id;
        },
        onUnlinkMedia() {
            this.merchant.mediaId = null;
        },

        // Marker
        setMarkerItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.merchant.markerId = targetId;
                this.merchant.marker = updatedMedia;
            });
        },
        onDropMarker(dragData) {
            this.setMarkerItem({targetId: dragData.id});
        },
        setMarkerFromSidebar(mediaEntity) {
            this.merchant.markerId = mediaEntity.id;
        },
        onUnlinkMarker() {
            this.merchant.markerId = null;
        },

        // Marker Shadow
        setMarkerShadowItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.merchant.markerShadowId = targetId;
                this.merchant.markerShadow = updatedMedia;
            });
        },
        onDropMarkerShadow(dragData) {
            this.setMarkerShadowItem({targetId: dragData.id});
        },
        setMarkerShadowFromSidebar(mediaEntity) {
            this.merchant.markerShadowId = mediaEntity.id;
        },
        onUnlinkMarkerShadow() {
            this.merchant.markerShadowId = null;
        }
    }
});
