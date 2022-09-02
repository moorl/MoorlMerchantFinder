const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;
const {mapPropertyErrors} = Shopware.Component.getComponentHelper();

import template from './index.html.twig';

Component.register('moorl-merchant-finder-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            item: {},
            isLoading: false,
            processSuccess: false,
            mediaModalIsOpen: false,
            timezoneOptions: []
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

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        criteria() {
            const criteria = new Criteria();

            criteria
                .addAssociation('tags')
                .addAssociation('categories')
                .addAssociation('productManufacturers')
                .addAssociation('salesChannels');

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
            this.loadTimezones();
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

        onClickSave() {
            this.isLoading = true;

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

        saveFinish() {
            this.processSuccess = false;
        },

        setMediaItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.item.mediaId = targetId;
                this.item.media = updatedMedia;
            });
        },
        onDropMedia(dragData) {
            this.setMediaItem({targetId: dragData.id});
        },
        setMediaFromSidebar(mediaEntity) {
            this.item.mediaId = mediaEntity.id;
        },
        onUnlinkMedia() {
            this.item.mediaId = null;
        },
        onCloseModal() {
            this.mediaModalIsOpen = false;
        },
        onSelectionChanges(mediaEntity) {
            this.item.mediaId = mediaEntity[0].id;
            this.item.media = mediaEntity[0];
        },
        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },
        loadTimezones() {
            return Shopware.Service('timezoneService').loadTimezones()
                .then((result) => {
                    this.timezoneOptions.push({
                        label: 'UTC',
                        value: 'UTC',
                    });

                    const loadedTimezoneOptions = result.map(timezone => ({
                        label: timezone,
                        value: timezone,
                    }));

                    this.timezoneOptions.push(...loadedTimezoneOptions);
                });
        },
    }
});
