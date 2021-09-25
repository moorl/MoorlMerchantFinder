const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

import template from './index.html.twig';

Component.register('sw-customer-detail-mf', {
    template,

    inject: ['repositoryFactory', 'context'],

    props: {
        customer: {
            type: Object,
            required: true,
        },

        customerEditMode: {
            type: Boolean,
            required: true,
            default: false,
        },

        isLoading: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    computed: {
        merchantCustomerFilterColumns() {
            return [
                'merchant.name',
                'merchant.email',
                'merchant.city',
                'customerNumber',
                'info'
            ];
        },

        merchantCustomerCriteria() {
            const criteria = new Criteria();

            criteria.addAssociation('customer');
            criteria.addAssociation('merchant');

            return criteria;
        }
    },

    methods: {}
});
