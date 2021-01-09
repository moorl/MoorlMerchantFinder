const { Application, Component, Mixin, Context } = Shopware;
const { Criteria } = Shopware.Data;

import template from './index.html.twig';

const initContainer = Application.getContainer('init');
const httpClient = initContainer.httpClient;

Component.register('moorl-merchant-marker-list', {
    template,

    inject: [
        'repositoryFactory',
        'numberRangeService',
        'context'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            items: null,
            sortBy: 'name',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        repository() {
            return this.repositoryFactory.create('moorl_merchant_marker');
        },

        searchContext() {
            return {
                ...Context.api,
                inheritance: true
            };
        },

        columns() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    routerLink: 'moorl.merchant.marker.detail',
                    label: this.$t('moorl-foundation.properties.name'),
                    allowResize: true
                },
                {
                    property: 'type',
                    dataIndex: 'type',
                    routerLink: 'moorl.merchant.marker.detail',
                    label: this.$t('moorl-foundation.properties.type'),
                    allowResize: true
                }
            ]
        }
    },

    created() {
        // getList() will be called by listing (mixin)
    },

    methods: {
        getList() {
            const criteria = new Criteria(this.page, this.limit, this.term);
            const params = this.getListingParams();
            this.naturalSorting = this.sortBy === 'name';
            this.sortDirection = params.sortDirection || 'ASC';
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.isLoading = true;
            this.repository.search(criteria, this.searchContext).then((searchResult) => {
                this.items = searchResult;
                this.total = searchResult.total;

                this.isLoading = false;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        changeLanguage() {
            this.getList();
        },

        updateSelection() {},

        updateTotal({total}) {
            this.total = total;
        },

        onDuplicate(reference) {
            this.repository.clone(reference.id, Shopware.Context.api, {
                name: `${reference.name} ${this.$tc('sw-product.general.copy')}`,
                locked: false
            }).then((duplicate) => {
                this.$router.push({name: 'moorl.merchant.marker.detail', params: {id: duplicate.id}});
            });
        }
    }
});
