const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;
const {mapPropertyErrors} = Shopware.Component.getComponentHelper();

import template from './index.html.twig';

Component.register('moorl-merchant-finder-detail', {
    template,

    inject: [
        'repositoryFactory',
        'seoUrlService'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            item: {},
            isLoading: false,
            processSuccess: false
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        ...mapPropertyErrors('item', [
            'name',
            'street',
            'zipcode',
            'city',
            'countryId',
            'email'
        ]),

        identifier() {
            if (this.item && this.item.name) {
                return this.item.name;
            }

            return this.$tc('sw-event-action.detail.titleNewEntity');
        },

        repository() {
            return this.repositoryFactory.create('moorl_merchant');
        },

        criteria() {
            const criteria = new Criteria();

            criteria
                .addAssociation('marker')
                .addAssociation('tags')
                .addAssociation('categories')
                .addAssociation('productManufacturers')
                .addAssociation('salesChannels');

            criteria.getAssociation('seoUrls')
                .addFilter(Criteria.equals('isCanonical', true));

            return criteria;
        },

        deliveryTypeOptions() {
            return [
                {
                    label: 'area',
                    value: 'area',
                },
                {
                    label: 'radius',
                    value: 'radius',
                }
            ]
        },

        merchantCustomerFilterColumns() {
            return [
                'customer.customerNumber',
                'customer.email',
                'customer.lastName',
                'customerNumber',
                'info'
            ];
        },

        merchantCustomerCriteria() {
            const criteria = new Criteria();

            criteria.addAssociation('customer');
            criteria.addAssociation('merchant');

            return criteria;
        },

        merchantStockFilterColumns() {
            return [
                'product.name',
                'product.productNumber',
                'isStock',
                'stock',
                'deliveryTime.name'
            ];
        },

        merchantStockCriteria() {
            const criteria = new Criteria();

            criteria.addAssociation('product');
            criteria.addAssociation('merchant');
            criteria.addAssociation('deliveryTime');

            return criteria;
        },

        merchantAreaFilterColumns() {
            return [
                'zipcode',
                'deliveryTime',
                'deliveryPrice',
                'minOrderValue',
                'merchant.name'
            ];
        },

        merchantAreaCriteria() {
            const criteria = new Criteria();

            criteria.addAssociation('merchant');

            return criteria;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getItem();
        },

        getItem() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api, this.criteria)
                .then((entity) => {
                    this.item = entity;
                });
        },

        onChangeLanguage() {
            this.getItem();
        },

        async onClickSave() {
            this.isLoading = true;

            await this.updateSeoUrls();

            if (this.item.openingHours === false) {
                this.item.openingHours = null;
            }

            this.repository
                .save(this.item, Shopware.Context.api)
                .then(() => {
                    this.getItem();
                    this.isLoading = false;
                    this.processSuccess = true;
                })
                .catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$tc('global.notification.notificationSaveErrorMessageRequiredFieldsInvalid'),
                    });
                });
        },

        updateSeoUrls() {
            if (!Shopware.State.list().includes('swSeoUrl')) {
                return Promise.resolve();
            }

            const seoUrls = Shopware.State.getters['swSeoUrl/getNewOrModifiedUrls']();

            return Promise.all(seoUrls.map((seoUrl) => {
                if (seoUrl.seoPathInfo) {
                    seoUrl.isModified = true;
                    return this.seoUrlService.updateCanonicalUrl(seoUrl, seoUrl.languageId);
                }

                return Promise.resolve();
            }));
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
