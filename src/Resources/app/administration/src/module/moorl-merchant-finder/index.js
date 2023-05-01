const {Module, Application} = Shopware;
import './page/list';
import './page/_detail';
import './page/create';
import './search-service';
import './style/main.scss';

import defaultSearchConfiguration from './default-search-configuration';

Module.register('moorl-merchant-finder', {
    type: 'plugin',
    name: 'moorl-merchant-finder',
    title: 'moorl-merchant-finder.general.mainMenuItemGeneral',
    color: '#ff3d58',
    icon: 'regular-globe-stand',
    entity: 'moorl_merchant',

    routes: {
        list: {
            component: 'moorl-merchant-finder-list',
            path: 'list'
        },
        detail: {
            component: 'moorl-merchant-finder-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'moorl.merchant.finder.list'
            }
        },
        create: {
            component: 'moorl-merchant-finder-create',
            path: 'create',
            meta: {
                parentPath: 'moorl.merchant.finder.list'
            }
        }
    },

    navigation: [{
        label: 'moorl-merchant-finder.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'moorl.merchant.finder.list',
        icon: 'regular-globe-stand',
        position: 40,
        parent: 'sw-catalogue'
    }],

    defaultSearchConfiguration
});

const SearchTypeService = Shopware.Service('searchTypeService');

SearchTypeService.upsertType('moorl_merchant', {
    entityName: 'moorl_merchant',
    placeholderSnippet: 'moorl-merchant-finder.general.placeholderSearchBar',
    listingRoute: 'moorl.merchant.finder.list'
});
