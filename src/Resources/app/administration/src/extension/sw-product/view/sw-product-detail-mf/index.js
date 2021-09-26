const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;
const {mapState} = Shopware.Component.getComponentHelper();

import template from './index.html.twig';

Component.register('sw-product-detail-mf', {
    template,

    inject: ['repositoryFactory', 'context'],

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            isLoading: false
        };
    },

    computed: {
        ...mapState('swProductDetail', [
            'product'
        ]),

        product() {
            const product = Shopware.State.get('swProductDetail').product;

            if (!product.customFields) {
                this.$set(product, 'customFields', {});
            }

            return product;
        },

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
    }
});
