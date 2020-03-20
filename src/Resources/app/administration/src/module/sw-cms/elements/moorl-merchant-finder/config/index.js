const { Component, Mixin, StateDeprecated } = Shopware;
import template from './index.html.twig';
import './index.scss';

Component.register('sw-cms-el-config-moorl-merchant-finder', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {};
    },

    computed: {
        moorlFoundation() {
            return MoorlFoundation;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('moorl-merchant-finder');
        }
    }
});
