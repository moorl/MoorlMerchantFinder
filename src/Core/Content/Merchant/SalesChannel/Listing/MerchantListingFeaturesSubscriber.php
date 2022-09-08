<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Listing;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSuggestCriteriaEvent;
use MoorlFoundation\Core\Service\LocationService;
use MoorlFoundation\Core\Service\SortingService;
use MoorlFoundation\Core\System\EntityListingFeaturesSubscriberExtension;
use Shopware\Core\Content\Product\SalesChannel\Listing\FilterCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class MerchantListingFeaturesSubscriber extends EntityListingFeaturesSubscriberExtension implements EventSubscriberInterface
{
    public function __construct(
        SortingService $sortingService,
        LocationService $locationService
    )
    {
        $this->sortingService = $sortingService;
        $this->locationService = $locationService;
        $this->entityName = MerchantDefinition::ENTITY_NAME;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MerchantListingCriteriaEvent::class => [
                ['handleListingRequest', 100],
                ['handleFlags', -100],
            ],
            MerchantSuggestCriteriaEvent::class => [
                ['handleFlags', -100],
            ],
            MerchantSearchCriteriaEvent::class => [
                ['handleSearchRequest', 100],
                ['handleFlags', -100],
            ],
            MerchantListingResultEvent::class => [
                ['handleResult', 100]
            ],
            MerchantSearchResultEvent::class => 'handleResult',
        ];
    }

    protected function getFilters(Request $request, SalesChannelContext $context): FilterCollection
    {
        $filters = new FilterCollection();

        $filters->add($this->getRadiusFilter($request));
        $filters->add($this->getManufacturerFilter($request));
        $filters->add($this->getCountryFilter($request));
        $filters->add($this->getTagFilter($request));

        return $filters;
    }
}
