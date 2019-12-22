const { Application, Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

const Papa = require('papaparse');

import template from './moorl-merchant-finder-list.html.twig';

Component.register('moorl-merchant-finder-list', {
    template,

    inject: [
        'repositoryFactory',
        'context'
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
            sortBy: 'company',
            showImportModal: false,
            isLoading: true,
            selectedFile: null,
            isImporting: false,
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

        columns() {
            return [{
                property: 'active',
                dataIndex: 'active',
                label: this.$t('moorl-merchant-finder.properties.active'),
                inlineEdit: 'boolean',
                allowResize: true,
                align: 'center'
            }, {
                property: 'company',
                dataIndex: 'company',
                label: this.$t('moorl-merchant-finder.properties.company'),
                routerLink: 'moorl.merchant.finder.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'zipcode',
                dataIndex: 'zipcode',
                label: this.$t('moorl-merchant-finder.properties.zipcode'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'city',
                dataIndex: 'city',
                label: this.$t('moorl-merchant-finder.properties.city'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'email',
                dataIndex: 'email',
                label: this.$t('moorl-merchant-finder.properties.email'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'locationLon',
                dataIndex: 'locationLon',
                label: this.$t('moorl-merchant-finder.properties.location'),
                allowResize: true,
                align: 'center'
            }];
        }
    },

    created() {

        this.repository = this.moorlMerchantRepository;

        this.csv = {
            data: [],
            matches: 0,
            schemaProperties: Object.keys(this.moorlMerchantRepository.schema.properties),
            filterSchemaProperties: [
                'id',
                'mediaId',
                'productManufacturerId',
                'customFields',
                'autoIncrement',
                'data',
                'createdAt',
                'updatedAt',
                'media',
            ],
            csvProperties: [],
            propertyMapping: {},
            options: {
                overwrite: true,
                getPosition: false,
                silentMode: false,
            }
        };

        this.initializeFurtherComponents();

        this.getList();

    },

    methods: {

        initializeFurtherComponents() {

            this.salesChannelRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.salesChannels = searchResult;
            });

            const mediaCriteria = new Criteria(1, 500);
            mediaCriteria.addSorting(Criteria.sort('fileName'));
            this.mediaRepository.search(mediaCriteria, Shopware.Context.api).then((searchResult) => {
                this.medias = searchResult;
            });

            this.customerGroupRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.customerGroups = searchResult;
            });

            const countryCriteria = new Criteria(1, 100);
            countryCriteria.addSorting(Criteria.sort('name'));
            this.countryRepository.search(countryCriteria, Shopware.Context.api).then((searchResult) => {
                this.countries = searchResult;
            });

        },

        getList() {
            const criteria = new Criteria(this.page, this.limit);
            this.isLoading = true;
            this.naturalSorting = this.sortBy === 'company';

            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.moorlMerchantRepository.search(criteria, Shopware.Context.api).then((items) => {
                this.total = items.total;
                this.tax = items;
                this.isLoading = false;
                this.merchants = items;
                return items;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        updateSelection() {
            console.log("ok");
        },
        updateTotal({total}) {
            this.total = total;
        },
        onClickUpload() {
            this.$refs.fileInput.click();
        },
        onFileInputChange() {
            const that = this;
            Papa.parse(this.$refs.fileInput.files[0], {
                header: true,
                skipEmptyLines: true,
                complete: function (results, file) {
                    console.log("NOTICE: Parsing complete", results, file);
                    that.csv.data = results.data;
                    that.validateCsv();
                    that.$refs.fileForm.reset();
                }
            });
        },

        validateCsv() {

            const that = this;
            let result = false;

            this.csv.csvProperties = Object.keys(this.csv.data[0]);
            this.csv.matches = 0;

            this.csv.schemaProperties.forEach(function (schemaProperty) {

                result = that.csv.csvProperties.indexOf(schemaProperty);

                if (result != -1) {
                    that.csv.propertyMapping[schemaProperty] = that.csv.csvProperties[result];
                    that.csv.matches++;
                }

            });

            this.showImportModal = true;

        },

        onClickImport() {
            this.createSystemNotificationSuccess({
                title: this.$t('moorl-merchant-finder.notification.importTitle'),
                message: this.$t('moorl-merchant-finder.notification.importText'),
                autoClose: false,
                isLoading: true,
            });
           this.importCsvRow();
        },

        importCsvRow() {
            this.isImporting = true;
            this.showImportModal = false;
            let item = this.csv.data.shift();
            item = this.sanitizeItem(item);
            if (this.csv.options.getPosition) {
                this.getPositionByAddress(item);
            } else {
                this.prepareSaveItem(item);
            }
        },

        sanitizeItem(item) {

            console.log("sanitizeItem() ", item);

            const that = this;
            let regex=/^\s*(true|1|on|yes|ja|an)\s*$/i; // For Type = boolean
            let newItem = {};

            this.csv.schemaProperties.forEach(function (schemaProperty) {

                if (typeof that.csv.propertyMapping[schemaProperty] == 'string') {

                    let propertyType = that.moorlMerchantRepository.schema.properties[schemaProperty].type;

                    switch (propertyType) {
                        case 'boolean':
                            if (['1','0'].indexOf(that.csv.propertyMapping[schemaProperty]) != -1) {
                                newItem[schemaProperty] = regex.test(that.csv.propertyMapping[schemaProperty]);
                            } else  {
                                newItem[schemaProperty] = regex.test(item[that.csv.propertyMapping[schemaProperty]]);
                            }
                            break;
                        case 'int':
                            newItem[schemaProperty] = parseInt(item[that.csv.propertyMapping[schemaProperty]]);
                            break;
                        case 'float':
                            newItem[schemaProperty] = parseFloat(item[that.csv.propertyMapping[schemaProperty]]);
                            break;
                        case 'uuid':
                            if (that.csv.propertyMapping[schemaProperty].length == 32) {
                                newItem[schemaProperty] = that.csv.propertyMapping[schemaProperty];
                            }
                            break;
                        case 'date':
                            // Do nothing
                            break;
                        default:
                            newItem[schemaProperty] = item[that.csv.propertyMapping[schemaProperty]];
                    }

                }

            });

            return newItem;

        },

        prepareSaveItem(item) {

            console.log("prepareSaveItem()", item);

            if (item.originId && this.csv.options.overwrite) {

                const merchantCriteria = new Criteria();
                merchantCriteria.addFilter(Criteria.equals('originId', item.originId));

                this.moorlMerchantRepository
                    .search(merchantCriteria, Shopware.Context.api)
                    .then((result) => {
                        if (result.length == 0) {
                            result = this.moorlMerchantRepository.create(Shopware.Context.api);
                            Object.assign(result, item);
                            this.saveItem(result);
                        } else {
                            result = result.first();
                            Object.assign(result, item);
                            this.saveItem(result);
                        }
                    });

            } else {
                let result = this.moorlMerchantRepository.create(Shopware.Context.api);
                Object.assign(result, item);
                this.saveItem(result);
            }

        },

        saveItem(item) {

            console.log("saveItem()", item);

            this.moorlMerchantRepository
                .save(item, Shopware.Context.api)
                .then(() => {
                    if (this.csv.data.length != 0) {
                        this.importCsvRow();
                    } else {
                        this.isImporting = false;
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

        getPositionByAddress(item) {

            console.log("getPositionByAddress()", item);

            const that = this;

            ['zipcode', 'city', 'street', 'countryCode'].forEach(function (checkMe) {
                if (typeof item[checkMe] != 'string' || item[checkMe].trim() == "") {
                    console.log("NOTICE: missing property", checkMe);
                    that.prepareSaveItem(item);
                    return;
                }
            });

            const initContainer = Application.getContainer('init');
            const httpClient = initContainer.httpClient;
            const searchParams = new URLSearchParams({
                "format": "json",
                "zipcode": item.zipcode,
                "city": item.city,
                "street": item.street + " " + item.streetNumber,
                "country": item.countryCode
            });

            httpClient.get(`http://nominatim.openstreetmap.org/search?` + searchParams).then((response) => {
                console.log(response);
                if (response.data.length > 0) {
                    console.log("NOTICE: Position found");
                    item.locationLon = parseFloat(response.data[0].lon);
                    item.locationLat = parseFloat(response.data[0].lat);
                } else {
                    console.log("NOTICE: No Position found");
                }
                this.prepareSaveItem(item);
            }).catch((exception) => {
                console.log(exception);
                this.isImporting = false;
                throw exception;
            });

        },

        onCloseModal() {
            this.showImportModal = false;
            this.showExportModal = false;
            this.showModal = false;
        },

        onRefresh() {
            this.getList();
        },

        onClickDownload() {

            console.log("onClickDownload()");

            const initContainer = Application.getContainer('init');
            const httpClient = initContainer.httpClient;

            httpClient.get("/moorl/merchant-finder/export").then((response) => {
                console.log(response.data);

                let a         = document.createElement('a');
                a.href        = 'data:attachment/csv,' +  encodeURIComponent(response.data);
                a.target      = '_blank';
                a.download    = 'export.csv';

                document.body.appendChild(a);
                a.click();

            });

        },

    }

});
