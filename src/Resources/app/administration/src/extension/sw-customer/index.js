const {Module} = Shopware;

import './page/sw-customer-detail';
import './view/sw-customer-detail-mf';

Module.register('sw-customer-detail-mf-tab', {
    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.customer.detail') {
            currentRoute.children.push({
                name: 'sw.customer.detail.mf',
                path: '/sw/customer/detail/:id/mf',
                component: 'sw-customer-detail-mf',
                meta: {
                    parentPath: "sw.customer.index"
                }
            });
        }
        next(currentRoute);
    }
});
