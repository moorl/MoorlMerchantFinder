import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    hidden: true,
    name: 'moorl-merchant-finder-basic',
    label: 'moorl-cms.blocks.general.merchantFinderBasic.label',
    category: 'moorl-cms-old',
    component: 'sw-cms-block-moorl-merchant-finder-basic',
    previewComponent: 'sw-cms-preview-moorl-merchant-finder-basic',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        one: {
            type: 'moorl-merchant-finder'
        }
    }
});
