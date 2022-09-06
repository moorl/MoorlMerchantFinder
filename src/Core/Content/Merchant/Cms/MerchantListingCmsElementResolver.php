<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use MoorlFoundation\Core\Content\Cms\FoundationListingCmsElementResolver;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class MerchantListingCmsElementResolver extends FoundationListingCmsElementResolver
{
    public function getType(): string
    {
        return 'merchant-listing';
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new MerchantListingStruct();
        $slot->setData($data);

        $request = $resolverContext->getRequest();
        $salesChannelContext = $resolverContext->getSalesChannelContext();

        $navigationId = $this->getNavigationId($resolverContext);

        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addAssociation('country');

        $this->enrichCmsElementResolverCriteriaV2($slot, $criteria, $resolverContext);

        $listing = $this->listingRoute
            ->load($navigationId, $request, $salesChannelContext, $criteria)
            ->getResult();

        $data->setListing($listing);
    }
}
