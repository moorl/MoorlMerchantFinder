Shopware.Component.override('sw-cms-create', {
    methods: {
        __onWizardComplete() {
            if (this.page.type === 'product_list' || this.page.type === 'product_detail' || this.page.type === 'merchant_detail') {
                this.onPageTypeChange();
            }

            this.wizardComplete = true;
            this.onSave();
        },
        _onWizardComplete() {
            if (this.page.type === 'merchant_detail') {
                this.onPageTypeChange();
            }

            this.$super('onWizardComplete')
        }
    }
});
