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
            sortBy: 'company',
            showImportModal: false,
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
        async getItemById(repo, originId, id) {
            const criteria = new Criteria();

            if (id && originId) {
                criteria.addFilter(Criteria.multi('OR', [
                    Criteria.equals('id', id),
                    Criteria.equals('originId', originId)
                ]));
            } else if (id) {
                criteria.addFilter(Criteria.equals('id', id));
            } else if (originId) {
                criteria.addFilter(Criteria.equals('originId', originId));
            } else {
                return null;
            }

            let entity = null;
            await repo.search(criteria, Shopware.Context.api).then((result) => {
                entity = result.first();
            });
            return entity ? entity : null;
        },

        async getIdByFileNameNew(filename) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('fileName', filename));
            let media = null;
            await this.mediaRepository.search(criteria, Shopware.Context.api).then((result) => {
                media = result.first();
            });
            return media ? media.id : null;
        },

        initializeFurtherComponents() {
            this.salesChannelRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.salesChannels = searchResult;
            });

            this.productManufacturerRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.manufacturers = searchResult;
                this.collections.product_manufacturer = searchResult;
            });

            this.categoryRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.categories = searchResult;
                this.collections.category = searchResult;
            });

            this.tagRepository.search(new Criteria(1, 100), Shopware.Context.api).then((searchResult) => {
                this.tags = searchResult;
                this.collections.tag = searchResult;
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

        async importCsvRow() {
            this.isLoading = true;
            this.showImportModal = false;
            let item = this.csv.data.shift();
            item = await this.sanitizeItem(item);

            console.log("sanitized...");
            console.log(item);

            if (this.csv.options.getPosition) {
                this.getLocationFromNominatim(item, (item) => this.prepareSaveItem(item))
                //this.getPositionByAddress(item);
            } else {
                this.prepareSaveItem(item);
            }
        },

        async sanitizeItem(item) {
            console.log("sanitizeItem() ", item);

            const that = this;
            let regex = /^\s*(true|1|on|yes|ja|an)\s*$/i; // For Type = boolean
            let newItem = {};

            for (let index = 0; index < this.csv.schemaProperties.length; index++) {
                let schemaProperty = this.csv.schemaProperties[index];

                if (typeof that.csv.propertyMapping[schemaProperty] == 'string') {
                    let property = that.moorlMerchantRepository.schema.properties[schemaProperty];

                    switch (property.type) {
                        case 'association':
                            if (property.relation == 'many_to_one') {
                                //console.log(property);
                                //console.log(that.csv.propertyMapping);
                                //console.log(item);
                                if (property.entity == 'media' && item[that.csv.propertyMapping[schemaProperty]].length > 0) {
                                    if (!that.csv.propertyMapping[property.localField] || that.csv.propertyMapping[property.localField].length != 32) {
                                        const newMediaItem = that.mediaRepository.create(Shopware.Context.api);
                                        const mediaUrl = new URL(item[that.csv.propertyMapping[schemaProperty]]);
                                        const file = mediaUrl.pathname.split('/').pop().split('.');

                                        if (file.length === 1) {
                                            newMediaItem.fileName = file[0].replace(/[^a-zA-Z0-9_\- ]/g, "");
                                        } else {
                                            newMediaItem.fileName = file[0].replace(/[^a-zA-Z0-9_\- ]/g, "");
                                            newMediaItem.fileExtension = file.pop();
                                        }
                                        newMediaItem.mediaFolderId = this.csv.options.mediaFolderId;
                                        let mediaId = await that.getIdByFileNameNew(newMediaItem.fileName);

                                        if (mediaId) {
                                            newItem[property.localField] = mediaId;
                                        } else {
                                            newItem[property.localField] = newMediaItem.id;
                                            that.mediaRepository.save(newMediaItem, Shopware.Context.api).then(() => {
                                                that.mediaService.uploadMediaFromUrl(
                                                    newMediaItem.id,
                                                    mediaUrl,
                                                    newMediaItem.fileExtension,
                                                    newMediaItem.fileName
                                                );
                                            });
                                        }
                                    }
                                }
                            } else if (property.relation == 'many_to_many') {
                                // Split string - If uuid then ok, if not uuid then get uuid from entity name
                                let parts = item[that.csv.propertyMapping[schemaProperty]].split("|");
                                if (parts[0].length == 32) {
                                    newItem[schemaProperty] = parts.map(function (id) {
                                        // TODO: Clean validation of all relationsship data
                                        if (!that.collections[property.entity].has(id)) {
                                            that.createNotificationError({
                                                title: that.$t('moorl-merchant-finder.notification.errorTitle'),
                                                message: schemaProperty + " - import validation error: unknown ID (" + id + ")",
                                                autoClose: false
                                            });
                                            return false;
                                        } else {
                                            return {id: id};
                                        }
                                    });
                                } else if (parts[0].length > 0) {
                                    // TODO: Try to auto add new Entities by name
                                    if (property.entity == 'tag') {
                                        let tagsCollection = [];
                                        that.tags.forEach(function (tag) {
                                            for (let i = 0; i < parts.length; i++) {
                                                if (tag.name == parts[i]) {
                                                    tagsCollection.push(tag);
                                                    parts.splice(i, 1);
                                                }
                                            }
                                        });
                                        for (let i = 0; i < parts.length; i++) {
                                            let result = that.tagRepository.create(Shopware.Context.api);
                                            result.name = parts[i];
                                            result.id = Shopware.Utils.createId();
                                            that.tagRepository.save(result, Shopware.Context.api);
                                            that.tags.add(result);
                                            tagsCollection.push(result);
                                        }
                                        newItem[schemaProperty] = tagsCollection;
                                    }
                                }
                            }
                            break;
                        case 'boolean':
                            if (['1', '0'].indexOf(that.csv.propertyMapping[schemaProperty]) != -1) {
                                newItem[schemaProperty] = regex.test(that.csv.propertyMapping[schemaProperty]);
                            } else {
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
                            if (item[that.csv.propertyMapping[schemaProperty]].length == 32) {
                                newItem[schemaProperty] = item[that.csv.propertyMapping[schemaProperty]];
                            }
                            break;
                        case 'date':
                            // Do nothing
                            break;
                        default:
                            newItem[schemaProperty] = item[that.csv.propertyMapping[schemaProperty]];
                    }

                }

            }

            return newItem;
        },

        async prepareSaveItem(item) {
            console.log("prepareSaveItem()", item);
            let merchant = await this.getItemById(this.moorlMerchantRepository, item.originId, item.id);
            if (!merchant || !this.csv.options.overwrite) {
                merchant = this.moorlMerchantRepository.create(Shopware.Context.api);
            }
            item.id = merchant.id;
            Object.assign(merchant, item);
            this.saveItem(merchant);
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

            if (typeof item.streetNumber != 'undefined' && item.streetNumber.trim() != "") {
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
                console.log(response);
                if (response.data.length > 0) {
                    console.log("NOTICE: Position found");
                    item.locationLon = parseFloat(response.data[0].lon);
                    item.locationLat = parseFloat(response.data[0].lat);
                } else {
                    console.log("NOTICE: No Position found");
                    this.createNotificationError({
                        title: this.$t('moorl-merchant-finder.notification.nominatimErrorTitle'),
                        message: this.$t('moorl-merchant-finder.notification.nominatimErrorText', 0, item)
                    });
                }
                this.prepareSaveItem(item);
            }).catch((exception) => {
                console.log(exception);
                this.isLoading = false;
                throw exception;
            });
        },

        onCloseModal() {
            this.showImportModal = false;
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
                        title: that.$t('moorl-foundation.detail.errorTitle'),
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
        }
    }
});
