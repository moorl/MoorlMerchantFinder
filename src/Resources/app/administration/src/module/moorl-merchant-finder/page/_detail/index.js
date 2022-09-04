const {Component, Mixin} = Shopware;
const {Criteria, ChangesetGenerator} = Shopware.Data;
const utils = Shopware.Utils;
const {mapPropertyErrors} = Shopware.Component.getComponentHelper();
const type = Shopware.Utils.types;
const {cloneDeep, merge} = Shopware.Utils.object;

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
            pageTypes: ['merchant_detail'],
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

        pageTypeCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addFilter(
                Criteria.equals('type', 'merchant_detail'),
            );

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
        },

        cmsPageRepository() {
            return this.repositoryFactory.create('cms_page');
        },

        cmsPageId() {
            return this.item ? this.item.cmsPageId : null;
        },

        cmsPage() {
            return Shopware.State.get('cmsPageState').currentPage;
        },
    },

    watch: {
        cmsPageId() {
            if (this.isLoading) {
                return;
            }

            if (this.item) {
                this.item.slotConfig = null;
                Shopware.State.dispatch('cmsPageState/resetCmsPageState')
                    .then(this.getAssignedCmsPage);
            }
        }
    },

    created() {
        Shopware.State.dispatch('cmsPageState/resetCmsPageState');

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

        getAssignedCmsPage() {
            if (this.cmsPageId === null) {
                return Promise.resolve(null);
            }

            const cmsPageId = this.cmsPageId;
            const criteria = new Criteria(1, 1);
            criteria.setIds([cmsPageId]);
            criteria.addAssociation('previewMedia');
            criteria.addAssociation('sections');
            criteria.getAssociation('sections').addSorting(Criteria.sort('position'));

            criteria.addAssociation('sections.blocks');
            criteria.getAssociation('sections.blocks')
                .addSorting(Criteria.sort('position', 'ASC'))
                .addAssociation('slots');

            return this.cmsPageRepository.search(criteria).then((response) => {
                const cmsPage = response.get(cmsPageId);

                if (cmsPageId !== this.cmsPageId) {
                    return null;
                }

                if (this.item.slotConfig !== null) {
                    cmsPage.sections.forEach((section) => {
                        section.blocks.forEach((block) => {
                            block.slots.forEach((slot) => {
                                if (this.item.slotConfig[slot.id]) {
                                    if (slot.config === null) {
                                        slot.config = {};
                                    }
                                    merge(slot.config, cloneDeep(this.item.slotConfig[slot.id]));
                                }
                            });
                        });
                    });
                }

                this.updateCmsPageDataMapping();
                Shopware.State.commit('cmsPageState/setCurrentPage', cmsPage);

                return this.cmsPage;
            });
        },

        updateCmsPageDataMapping() {
            Shopware.State.commit('cmsPageState/setCurrentMappingEntity', 'moorl_merchant');
            Shopware.State.commit(
                'cmsPageState/setCurrentMappingTypes',
                this.cmsService.getEntityMappingTypes('moorl_merchant'),
            );
            Shopware.State.commit('cmsPageState/setCurrentDemoEntity', this.item);
        },

        getCmsPageOverrides() {
            if (this.cmsPage === null) {
                return null;
            }

            this.deleteSpecifcKeys(this.cmsPage.sections);

            const changesetGenerator = new ChangesetGenerator();
            const {changes} = changesetGenerator.generate(this.cmsPage);

            const slotOverrides = {};
            if (changes === null) {
                return slotOverrides;
            }

            if (type.isArray(changes.sections)) {
                changes.sections.forEach((section) => {
                    if (type.isArray(section.blocks)) {
                        section.blocks.forEach((block) => {
                            if (type.isArray(block.slots)) {
                                block.slots.forEach((slot) => {
                                    slotOverrides[slot.id] = slot.config;
                                });
                            }
                        });
                    }
                });
            }

            return slotOverrides;
        },

        deleteSpecifcKeys(sections) {
            if (!sections) {
                return;
            }

            sections.forEach((section) => {
                if (!section.blocks) {
                    return;
                }

                section.blocks.forEach((block) => {
                    if (!block.slots) {
                        return;
                    }

                    block.slots.forEach((slot) => {
                        if (!slot.config) {
                            return;
                        }

                        Object.values(slot.config).forEach((configField) => {
                            if (configField.entity) {
                                delete configField.entity;
                            }
                            if (configField.hasOwnProperty('required')) {
                                delete configField.required;
                            }
                            if (configField.type) {
                                delete configField.type;
                            }
                        });
                    });
                });
            });
        },
    }
});
