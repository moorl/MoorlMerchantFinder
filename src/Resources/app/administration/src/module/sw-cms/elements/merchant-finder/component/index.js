const { Application, Component, Mixin } = Shopware;
import template from './sw-cms-el-merchant-finder.html.twig';
import './sw-cms-el-merchant-finder.scss';

Component.register('sw-cms-el-merchant-finder', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    computed: {
        styles() {
            return {
                'min-height': this.element.config.minHeight.value !== 0 ? this.element.config.minHeight.value : '340px'
            };
        },
    },

    watch: {
        cmsPageState: {
            deep: true,
            handler() {
                this.$forceUpdate();
            }
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('merchant-finder');
        }
    }
});
