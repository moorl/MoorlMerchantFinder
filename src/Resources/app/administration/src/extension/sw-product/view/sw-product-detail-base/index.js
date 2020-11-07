import template from './index.html.twig';

const { Component } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.override('sw-product-detail-base', {
    template,

    computed: {
        merchantStockFilterColumns() {
            return [
                'merchant.name',
                'merchant.originId',
                'isStock',
                'stock',
                'deliveryTime.name'
            ];
        },

        merchantStockCriteria() {
            const criteria = new Criteria();

            criteria.addAssociation('product');
            criteria.addAssociation('merchant');
            criteria.addAssociation('deliveryTime');

            return criteria;
        }
    },

    methods: {}
});
