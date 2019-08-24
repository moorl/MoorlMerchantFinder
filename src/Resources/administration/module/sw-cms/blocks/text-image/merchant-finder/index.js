import './component';
import './preview';

const { Application } = Shopware;

Application.getContainer('service').cmsService.registerCmsBlock({
    name: 'merchant-finder',
    label: 'Merchant Finder',
    category: 'text-image',
    component: 'sw-cms-block-merchant-finder',
    previewComponent: 'sw-cms-preview-merchant-finder',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        merchantFinder: 'merchant-finder'
    }
});