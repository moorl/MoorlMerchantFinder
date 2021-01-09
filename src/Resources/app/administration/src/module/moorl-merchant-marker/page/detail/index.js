import template from './index.html.twig';

const { Component, Mixin, StateDeprecated, Context } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;
const utils = Shopware.Utils;

Component.register('moorl-merchant-marker-detail', {
    template,

    inject: [
        'repositoryFactory',
        'context',
        'foundationApiService'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            item: null,
            isLoading: false,
            processSuccess: false,
            uploadTagMarker: utils.createId(),
            uploadTagMarkerRetina: utils.createId(),
            uploadTagMarkerShadow: utils.createId()
        };
    },

    computed: {
        repository() {
            return this.repositoryFactory.create('moorl_merchant_marker');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        defaultCriteria() {
            const criteria = new Criteria();
            return criteria;
        },

        searchCriteria() {
            const criteria = new Criteria();
            return criteria;
        },

        searchContext() {
            return {
                ...Context.api,
                inheritance: true
            };
        },

        markerSettings() {
            return {
                iconSizeX: 25,
                iconSizeY: 41,
                shadowSizeX: 41,
                shadowSizeY: 41,
                iconAnchorX: 12,
                iconAnchorY: 41,
                shadowAnchorX: 6,
                shadowAnchorY: 21,
                popupAnchorX: 1,
                popupAnchorY: -34
            };
        }
    },

    created() {
        this.getItem();
    },

    methods: {
        getItem() {
            this.repository
                .get(this.$route.params.id, this.searchContext, this.defaultCriteria)
                .then((entity) => {
                    this.item = entity;
                });
        },

        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.item, Shopware.Context.api)
                .then(() => {
                    this.getItem();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-foundation.notification.saveError'),
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

        // Marker
        setMarkerItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.item.markerId = targetId;
                this.item.marker = updatedMedia;
            });
        },
        onDropMarker(dragData) {
            this.setMarkerItem({targetId: dragData.id});
        },
        setMarkerFromSidebar(mediaEntity) {
            this.item.markerId = mediaEntity.id;
        },
        onUnlinkMarker() {
            this.item.markerId = null;
        },

        // Marker Shadow
        setMarkerRetinaItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.item.markerRetinaId = targetId;
                this.item.markerRetina = updatedMedia;
            });
        },
        onDropMarkerRetina(dragData) {
            this.setMarkerRetinaItem({targetId: dragData.id});
        },
        setMarkerRetinaFromSidebar(mediaEntity) {
            this.item.markerRetinaId = mediaEntity.id;
        },
        onUnlinkMarkerRetina() {
            this.item.markerRetinaId = null;
        },

        // Marker Shadow
        setMarkerShadowItem({targetId}) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.item.markerShadowId = targetId;
                this.item.markerShadow = updatedMedia;
            });
        },
        onDropMarkerShadow(dragData) {
            this.setMarkerShadowItem({targetId: dragData.id});
        },
        setMarkerShadowFromSidebar(mediaEntity) {
            this.item.markerShadowId = mediaEntity.id;
        },
        onUnlinkMarkerShadow() {
            this.item.markerShadowId = null;
        }
    }
});
