const {Application, Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;
const Papa = require('papaparse');

import template from './index.html.twig';

const initContainer = Application.getContainer('init');
const httpClient = initContainer.httpClient;

Component.register('moorl-merchant-finder-list', {
    template,

    inject: [
        'repositoryFactory',
        'context',
        'mediaService'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            repository: null,
            merchants: null,
            selectedItems: null,
            sortBy: 'company',
            showImportModal: false,
            showExportModal: false,
            isLoading: true,
            selectedFile: null,
            isImporting: false,
            showLocModal: false,
            getLocation: {
                overwrite: false,
                skipError: true,
                criteria: null,
                data: [],
                total: 0,
            },
            csv: {
                data: [],
                matches: 0,
                schemaProperties: null,
                filterSchemaProperties: [
                    'id',
                    'productManufacturerId',
                    'customFields',
                    'autoIncrement',
                    'data',
                    'createdAt',
                    'updatedAt',
                    'translated',
                    'distance',
                    'merchantOpeningHours',
                    'country',
                    'countryId',
                    'translations'
                ],
                csvProperties: [],
                propertyMapping: {},
                options: {
                    overwrite: true,
                    getPosition: false,
                    silentMode: false,
                    mediaFolderId: null,
                }
            }
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        moorlMerchantRepository() {
            return this.repositoryFactory.create('moorl_merchant');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        productManufacturerRepository() {
            return this.repositoryFactory.create('product_manufacturer');
        },

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        customerGroupRepository() {
            return this.repositoryFactory.create('customer_group');
        },

        countryRepository() {
            return this.repositoryFactory.create('country');
        },

        tagRepository() {
            return this.repositoryFactory.create('tag');
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        columns() {
            return [{
                property: 'active',
                dataIndex: 'active',
                label: this.$t('moorl-foundation.properties.active'),
                inlineEdit: 'boolean',
                allowResize: true,
                align: 'center'
            }, {
                property: 'type',
                dataIndex: 'type',
                label: this.$t('moorl-foundation.properties.type'),
                inlineEdit: 'string',
                align: 'center',
                allowResize: true,
            }, {
                property: 'name',
                dataIndex: 'name',
                label: this.$t('moorl-foundation.properties.name'),
                routerLink: 'moorl.merchant.finder.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            },{
                property: 'company',
                dataIndex: 'company',
                label: this.$t('moorl-foundation.properties.company'),
                routerLink: 'moorl.merchant.finder.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'countryCode',
                dataIndex: 'countryCode',
                label: this.$t('moorl-foundation.properties.zipcode'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'city',
                dataIndex: 'city',
                label: this.$t('moorl-foundation.properties.city'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'email',
                dataIndex: 'email',
                label: this.$t('moorl-foundation.properties.email'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'locationLon',
                dataIndex: 'locationLon',
                label: this.$t('moorl-foundation.properties.location'),
                allowResize: true,
                align: 'center'
            }, {
                property: 'priority',
                dataIndex: 'priority',
                label: this.$t('moorl-foundation.properties.priority'),
                inlineEdit: 'number',
                allowResize: true,
                align: 'right'
            }];
        }
    },

    created() {
        this.repository = this.moorlMerchantRepository;
        this.collections = {};
        this.csv.schemaProperties = Object.keys(this.moorlMerchantRepository.schema.properties);
        this.initializeFurtherComponents();
        this.getList();
    },

    methods: {
        initializeFurtherComponents() {},

        getItems() {
            this.getList();
        },

        getList() {
            const criteria = new Criteria(this.page, this.limit);
            this.isLoading = true;
            this.naturalSorting = this.sortBy === 'company';

            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.moorlMerchantRepository.search(criteria, Shopware.Context.api).then((items) => {
                this.total = items.total;
                this.isLoading = false;
                this.merchants = items;
                return items;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        updateSelection(selection) {
            console.log(selection);
            this.selectedItems = selection;
        },

        updateTotal({total}) {
            this.total = total;
        },

        saveItem(item) {
            console.log("saveItem()", item);

            this.moorlMerchantRepository
                .save(item, Shopware.Context.api)
                .then(() => {
                    if (this.csv.data.length != 0) {
                        this.createNotificationSuccess({
                            title: this.$t('moorl-merchant-finder.notification.progressTitle'),
                            message: this.$t('moorl-merchant-finder.notification.progressText', 0, {
                                remaining: this.csv.data.length
                            })
                        });
                        this.importCsvRow();
                    } else {
                        this.isLoading = false;
                        this.createSystemNotificationSuccess({
                            title: this.$t('moorl-merchant-finder.notification.successTitle'),
                            message: this.$t('moorl-merchant-finder.notification.successText')
                        });
                        this.getList();
                    }
                }).catch((exception) => {
                console.log(exception);
                this.createNotificationError({
                    title: this.$t('moorl-merchant-finder.notification.errorTitle'),
                    message: exception
                });
            });
        },

        onCloseModal() {
            this.showImportModal = false;
            this.showExportModal = false;
            this.showLocModal = false;
            this.showModal = false;
        },

        onRefresh() {
            this.getList();
        },

        onClickGetLocation() {
            console.log("onClickGetLocation()");
            this.showLocModal = true;
        },

        onStartGetLocation() {
            console.log("onStartGetLocation()");

            const criteria = new Criteria();

            criteria.addFilter(Criteria.not('OR', [
                Criteria.equals('street', null),
                Criteria.equals('city', null),
                Criteria.equals('zipcode', null),
                Criteria.equals('countryCode', null),
            ]));

            if (!this.getLocation.overwrite) {
                criteria.addFilter(Criteria.multi('OR', [
                    Criteria.equals('locationLat', null),
                    Criteria.equals('locationLon', null)
                ]));
            }

            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.getLocation.criteria = criteria;

            this.getListForGetLocation();
        },

        getListForGetLocation() {
            console.log("getListForGetLocation()");

            this.moorlMerchantRepository.search(this.getLocation.criteria, Shopware.Context.api).then((items) => {
                this.getLocation.data = [...this.getLocation.data, ...items];
                console.log(this.getLocation.data);

                if (this.getLocation.data.length < items.total) {
                    this.getListForGetLocation();
                } else {
                    this.getLocation.total = items.total;
                    this.proccessGetLocation();
                }
            }).catch(() => {
                this.isLoading = false;
            });
        },

        getLocationFromNominatim(item, callback) {
            console.log("getLocationFromNominatim()");

            for (let checkMe of ['zipcode', 'city', 'street', 'countryCode']) {
                if (!item[checkMe]) {
                    console.log("missing parameters for nominatim", checkMe);

                    item.locationLon = null;
                    item.locationLat = null;

                    this.createNotificationError({
                        title: this.$t('moorl-merchant-finder.notification.nominatimErrorTitle'),
                        message: this.$t('moorl-merchant-finder.notification.nominatimErrorText', 0, item)
                    });

                    callback(item);
                    return;
                }
            }

            if (item.streetNumber) {
                item.street = item.street + " " + item.streetNumber;
            }

            const searchParams = new URLSearchParams({
                "format": "json",
                "zipcode": item.zipcode,
                "city": item.city,
                "street": item.street,
                "country": item.countryCode
            });

            httpClient.get(`//nominatim.openstreetmap.org/search?` + searchParams).then((response) => {
                if (response.data.length > 0) {
                    item.locationLon = parseFloat(response.data[0].lon);
                    item.locationLat = parseFloat(response.data[0].lat);
                } else {
                    item.locationLon = null;
                    item.locationLat = null;

                    this.createNotificationError({
                        title: this.$t('moorl-merchant-finder.notification.nominatimErrorTitle'),
                        message: this.$t('moorl-merchant-finder.notification.nominatimErrorText', 0, item)
                    });
                }

                callback(item);
            }).catch((exception) => {
                console.log(exception);
                that.isLoading = false;
                throw exception;
            });
        },

        proccessGetLocation() {
            console.log("proccessGetLocation()");

            if (this.getLocation.data.length === 0) {
                return;
            }

            let item = this.getLocation.data.shift();
            console.log(item);

            this.getLocationFromNominatim(item, (item) => {
                this.moorlMerchantRepository
                    .save(item, Shopware.Context.api)
                    .then(() => {
                        this.proccessGetLocation();
                    }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: that.$t('moorl-foundation.notification.errorTitle'),
                        message: exception
                    });
                });
            });
        },

        onClickDownload() {
            console.log("onClickDownload()");

            httpClient.get("/moorl/merchant-finder/export").then((response) => {
                let a = document.createElement('a');
                a.href = 'data:attachment/csv,' + encodeURIComponent(response.data);
                a.target = '_blank';
                a.download = 'export.csv';
                document.body.appendChild(a);
                a.click();
            });
        },

        onChangeLanguage() {
            this.getList();
        },

        onImportModal() {
            this.showImportModal = true;
        },

        onExportModal() {
            this.showExportModal = true;
        }
    }
});
