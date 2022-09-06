import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'merchant-listing',
    plugin: 'MoorlMerchantFinder',
    icon: 'default-view-grid',
    color: '#c0eebe',
    previewComponent: true,
    label: 'sw-cms.elements.moorl-foundation-listing.name',
    component: 'sw-cms-el-merchant-listing',
    configComponent: 'sw-cms-el-config-merchant-listing',
    defaultConfig: {}
});
