const { Application } = Shopware;
import './component';
import './config';
import './preview';

Application.getContainer('service').cmsService.registerCmsElement({
    name: 'moorl-merchant-finder',
    label: 'Merchant Finder',
    component: 'sw-cms-el-moorl-merchant-finder',
    configComponent: 'sw-cms-el-config-moorl-merchant-finder',
    previewComponent: 'sw-cms-el-preview-moorl-merchant-finder',
    defaultConfig: {
        style: {
            source: 'static',
            value: 'results-map'
        },
        mapStyle: {
            source: 'static',
            value: 'basic'
        },
        colorScheme: {
            source: 'static',
            value: 'light'
        },
        minHeight: {
            source: 'static',
            value: 'calc(100vh - 310px)'
        },
        resultsWidth: {
            source: 'static',
            value: '500px'
        },
        categoryFilter: {
            source: 'static',
            value: true
        },
        tagFilter: {
            source: 'static',
            value: true
        },
        manufacturerFilter: {
            source: 'static',
            value: true
        },
        borders: {
            source: 'static',
            value: '20px'
        },
        items: {
            source: 'static',
            value: 500
        },
        autoSrollElement: {
            source: 'static',
            value: true
        }
    }
});
