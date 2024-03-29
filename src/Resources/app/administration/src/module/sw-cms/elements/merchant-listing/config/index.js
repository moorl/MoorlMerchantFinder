const {Component} = Shopware;
const {Criteria} = Shopware.Data;

import template from './index.html.twig';

Component.extend('sw-cms-el-config-merchant-listing', 'sw-cms-el-config-moorl-foundation-listing', {
    template,

    data() {
        return {
            entity: 'moorl_merchant',
            elementName: 'merchant-listing',
            criteria: (new Criteria(1, 12)).addAssociation('media'),
            contentRoute: 'moorl.merchant.finder.list'
        }
    }
});
