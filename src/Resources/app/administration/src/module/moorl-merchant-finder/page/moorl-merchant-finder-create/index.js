const { Component } = Shopware;

import template from '../moorl-merchant-finder-detail/moorl-merchant-finder-detail.html.twig';

Component.extend('moorl-merchant-finder-create', 'moorl-merchant-finder-detail', {
    template,
    methods: {
        getMerchant() {
            this.merchant = this.repository.create(Shopware.Context.api);
            this.isLoading = false;
        },
        onClickSave() {
            this.isLoading = true;
            this.repository
                .save(this.merchant, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({name: 'moorl.merchant.finder.detail', params: {id: this.merchant.id}});
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('moorl-merchant-finder.detail.errorTitle'),
                    message: exception
                });
            });
        }
    }
});
