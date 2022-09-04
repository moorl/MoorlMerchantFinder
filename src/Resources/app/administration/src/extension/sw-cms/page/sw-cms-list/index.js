Shopware.Component.override('sw-cms-list', {
    computed: {
        sortPageTypes() {
            const sortPageTypes = this.$super('sortPageTypes');

            sortPageTypes.push({
                value: 'merchant_detail',
                name: this.$tc('moorl-merchant-finder.general.mainMenuItemGeneral')
            });

            return sortPageTypes;
        },

        pageTypes() {
            const pageTypes = this.$super('pageTypes');

            pageTypes['merchant_detail'] = this.$tc('moorl-merchant-finder.general.mainMenuItemGeneral');

            return pageTypes;
        },
    }
});
