import template from './index.html.twig';

const { Component } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.override('moorl-merchant-finder-detail', {
    template,

    computed: {
        merchantStockFilterColumns() {
            return [
                'product.name',
                'product.productNumber',
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
