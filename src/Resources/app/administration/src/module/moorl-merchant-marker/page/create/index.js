const { Component } = Shopware;

import template from '../detail/index.html.twig';

Component.extend('moorl-merchant-marker-create', 'moorl-merchant-marker-detail', {
    template,
    methods: {
        getItem() {
            this.item = this.repository.create(Shopware.Context.api);
            if (!this.item.markerSettings) {
                this.item.markerSettings = this.markerSettings;
            }
            this.isLoading = false;
        },
        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.item, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({name: 'moorl.merchant.marker.detail', params: {id: this.item.id}});
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-foundation.notification.errorTitle'),
                    message: exception
                });
            });
        }
    }
});
