<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\Service\SortingService;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Country\CountryDefinition;
use Symfony\Component\HttpFoundation\Request;

class MoorlMerchantFinderCmsElementResolver extends AbstractCmsElementResolver
{
    private SortingService $sortingService;

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

        $criteria->addAggregation(new EntityAggregation(
            'moorl-merchant-finder-country',
            'moorl_merchant.country.id',
            CountryDefinition::ENTITY_NAME
        ));

        $criteria->addAggregation(new EntityAggregation(
            'moorl-merchant-finder-category',
            'moorl_merchant.category.id',
            CategoryDefinition::ENTITY_NAME
        ));

        $collection = new CriteriaCollection();
        $collection->add($slot->getUniqueIdentifier(), MerchantDefinition::class, $criteria);

        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new MoorlMerchantFinderStruct();
        $slot->setData($data);

        //dump($result);exit;
    }
}
