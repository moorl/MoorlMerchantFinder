const { Criteria } = Shopware.Data;

Shopware.Component.override('sw-cms-detail', {
    computed: {
        cmsPageTypes() {
            const cmsPageTypes = this.$super('cmsPageTypes');

            cmsPageTypes['merchant_detail'] = this.$tc('moorl-merchant-finder.general.mainMenuItemGeneral');

            return cmsPageTypes;
        },

        cmsTypeMappingEntities() {
            const cmsTypeMappingEntities = this.$super('cmsTypeMappingEntities');

            cmsTypeMappingEntities['merchant_detail'] = {
                entity: 'moorl_merchant',
                mode: 'single',
            };

            return cmsTypeMappingEntities;
        },

        cmsPageTypeSettings() {
            if (this.page.type === 'merchant_detail') {
                return {
                    entity: 'moorl_merchant',
                    mode: 'single',
                };
            }

            return this.$super('cmsPageTypeSettings');
        },
    },

    methods: {
        onDemoEntityChange(demoEntityId) {
            const demoMappingType = this.cmsPageTypeSettings?.entity;

            if (demoMappingType === 'moorl_merchant') {
                this.loadDemoCreator(demoEntityId);
                return;
            }

            this.$super('onDemoEntityChange');
        },

        async loadDemoCreator(entityId) {
            const criteria = new Criteria(1, 1);

            if (entityId) {
                criteria.setIds([entityId]);
            }

            const response = await this.repositoryFactory.create('moorl_merchant').search(criteria);
            const demoEntity = response[0];

            this.demoEntityId = demoEntity.id;
            Shopware.State.commit('cmsPageState/setCurrentDemoEntity', demoEntity);
        },

        _onPageTypeChange() {
            console.log('onPageTypeChange');
            console.log(this.page.type);

            if (this.page.type === 'merchant_detail') {
                this.processCreatorDetailType();
            }

            this.$super('onPageTypeChange');
        },

        _processCreatorDetailType() {
            console.log('processCreatorDetailType');

            const creatorDetailBlocks = [
                {
                    type: 'moorl-column-layout-1',
                    elements: [
                        {
                            slot: 'slot-a',
                            type: 'creator-header',
                            config: {},
                        }
                    ],
                },
            ];

            creatorDetailBlocks.forEach(block => {
                const newBlock = this.blockRepository.create();

                block.elements.forEach(el => { el.blockId = newBlock.id; });

                this.processBlock(newBlock, block.type);
                this.processElements(newBlock, block.elements);
            });
        }
    }
});
