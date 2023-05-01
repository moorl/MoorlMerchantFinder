import template from './sw-cms-create-wizard.html.twig';

Shopware.Component.override('sw-cms-create-wizard', {
    template,

    created() {
        this.pageTypeNames['merchant_detail'] = this.$tc('moorl-merchant-finder.general.mainMenuItemGeneral');
        this.pageTypeIcons['merchant_detail'] = 'regular-globe-stand';
    },
});
