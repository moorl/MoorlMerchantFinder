const {Module} = Shopware;

import './page/sw-product-detail';
import './view/sw-product-detail-mf';

Module.register('sw-product-detail-mf-tab', {
    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.mf',
                path: '/sw/product/detail/:id/mf',
                component: 'sw-product-detail-mf',
                meta: {
                    parentPath: "sw.product.index"
                }
            });
        }
        next(currentRoute);
    }
});
