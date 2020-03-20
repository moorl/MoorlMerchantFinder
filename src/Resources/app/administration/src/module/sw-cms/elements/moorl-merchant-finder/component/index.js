const { Application, Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
import template from './index.html.twig';
import './index.scss';

Component.register('sw-cms-el-moorl-merchant-finder', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    inject: [
        'repositoryFactory',
        'context'
    ],

    data() {
        return {
            merchants: null
        };
    },

    computed: {
        styles() {
            return {
                'min-height': this.element.config.minHeight.value !== 0 ? this.element.config.minHeight.value : '340px'
            };
        },

        searchStyle() {
            return {
                'margin-bottom': this.element.config.borders.value
            }
        },

        contentStyle() {

        },

        resultsStyle() {
            if (this.element.config.style.value === 'masonry') {
                return {
                    'column-width': this.element.config.maxDimensions.value,
                    'column-gap': this.element.config.borders.value
                }
            } else if (this.element.config.style.value === 'list') {
                return null;
            } else if (this.element.config.style.value === 'grid') {
                return {
                    'margin-right': '-' + this.element.config.borders.value,
                    'margin-left': '-' + this.element.config.borders.value
                }
            } else if (this.element.config.style.value === 'slider') {
                return null;
            }
        },

        mapStyle() {
            if (this.element.config.style.value === 'grid') {
                return {
                    'padding-right': this.element.config.borders.value,
                    'padding-left': this.element.config.borders.value,
                    'margin-bottom': this.element.config.borders.value
                }
            } else if (this.element.config.style.value === 'list') {
                return {
                    'margin-bottom': this.element.config.borders.value
                }
            } else if (this.element.config.style.value === 'masonry') {
                return {
                    'margin-bottom': this.element.config.borders.value
                }
            } else if (this.element.config.style.value === 'slider') {
                return {
                    'margin-bottom': this.element.config.borders.value
                }
            }
        },

        moorlMerchantRepository() {
            return this.repositoryFactory.create('moorl_merchant');
        }

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

        getList() {
            const criteria = new Criteria(1, 25);

            this.moorlMerchantRepository.search(criteria, Shopware.Context.api).then((items) => {
                this.merchants = items;
                this.isLoading = false;
            }).catch(() => {
            });
        },

        createdComponent() {
            this.initElementConfig('moorl-merchant-finder');
            this.getList();
        }
    }
});
