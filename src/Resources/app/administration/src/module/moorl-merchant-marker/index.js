const { Module } = Shopware;
import './page/list';
import './page/detail';
import './page/create';
import './style/main.scss';

Module.register('moorl-merchant-marker', {
    type: 'plugin',
    name: 'MerchantFinder',
    title: 'moorl-merchant-finder.general.markerMenuItemGeneral',
    color: '#ff3d58',
    icon: 'default-object-globe',
    routes: {
        list: {
            component: 'moorl-merchant-marker-list',
            path: 'list'
        },
        detail: {
            component: 'moorl-merchant-marker-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'moorl.merchant.marker.list'
            }
        },
        create: {
            component: 'moorl-merchant-marker-create',
            path: 'create',
            meta: {
                parentPath: 'moorl.merchant.marker.list'
            }
        }
    },
    navigation: [{
        label: 'moorl-merchant-finder.general.markerMenuItemGeneral',
        color: '#ff3d58',
        path: 'moorl.merchant.marker.list',
        icon: 'default-object-globe',
        position: 41,
        parent: 'sw-content'
    }]
});
