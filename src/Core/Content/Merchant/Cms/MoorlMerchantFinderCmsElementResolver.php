<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\MerchantAvailableFilter;
use MoorlFoundation\Core\Service\SortingService;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\System\Tag\TagDefinition;

class MoorlMerchantFinderCmsElementResolver extends AbstractCmsElementResolver
{
    private readonly SortingService $sortingService;

    public function __construct(
        SortingService $sortingService
    )
    {
        $this->sortingService = $sortingService;
    }

    public function getType(): string
    {
        return 'moorl-merchant-finder';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria();
        $this->sortingService->enrichCmsElementResolverCriteria(
            $slot,
            $criteria,
            $resolverContext->getSalesChannelContext()->getContext()
        );

        $criteria->addFilter(new MerchantAvailableFilter($resolverContext->getSalesChannelContext()));

        $criteria->addAggregation(new EntityAggregation(
            'countries',
            'country.id',
            CountryDefinition::ENTITY_NAME
        ));
        $criteria->addAggregation(new EntityAggregation(
            'categories',
            'categories.id',
            CategoryDefinition::ENTITY_NAME
        ));
        $criteria->addAggregation(new EntityAggregation(
            'tags',
            'tags.id',
            TagDefinition::ENTITY_NAME
        ));
        $criteria->addAggregation(new EntityAggregation(
            'productManufacturers',
            'productManufacturers.id',
            ProductManufacturerDefinition::ENTITY_NAME
        ));

        $criteria->setLimit(1);

        $collection = new CriteriaCollection();
        $collection->add($slot->getUniqueIdentifier(), MerchantDefinition::class, $criteria);

        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new MoorlMerchantFinderStruct();
        $slot->setData($data);

        $searchResult = $result->get($slot->getUniqueIdentifier());
        $aggregations = $searchResult->getAggregations();

        $data->setMerchants($searchResult->getEntities());
        $data->setCountries($aggregations->get('countries')->getEntities());
        $data->setCategories($aggregations->get('categories')->getEntities());
        $data->setTags($aggregations->get('tags')->getEntities());
        $data->setProductManufacturers($aggregations->get('productManufacturers')->getEntities());
    }
}
