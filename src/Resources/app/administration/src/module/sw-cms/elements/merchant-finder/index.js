const { Application } = Shopware;
import './component';
import './config';
import './preview';

Application.getContainer('service').cmsService.registerCmsElement({
    name: 'merchant-finder',
    label: 'Merchant Finder',
    component: 'sw-cms-el-merchant-finder',
    configComponent: 'sw-cms-el-config-merchant-finder',
    previewComponent: 'sw-cms-el-preview-merchant-finder',
    defaultConfig: {
        title: {
            source: 'static',
            value: ''
        },
        titleCss: {
            source: 'static',
            value: 'text-center'
        },
        minHeight: {
            source: 'static',
            value: '60vh'
        }
    }
});
