const { Component, Mixin, StateDeprecated } = Shopware;
import template from './sw-cms-el-config-merchant-finder.html.twig';
import './sw-cms-el-config-merchant-finder.scss';

Component.register('sw-cms-el-config-merchant-finder', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {};
    },

    computed: {},

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('merchant-finder');
        }
    }
});
