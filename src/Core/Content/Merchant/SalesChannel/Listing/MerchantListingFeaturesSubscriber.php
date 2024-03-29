<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Listing;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSuggestCriteriaEvent;
use MoorlFoundation\Core\Service\LocationServiceV2;
use MoorlFoundation\Core\Service\SortingService;
use MoorlFoundation\Core\System\EntityListingFeaturesSubscriberExtension;
use Shopware\Core\Content\Product\SalesChannel\Listing\FilterCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class MerchantListingFeaturesSubscriber extends EntityListingFeaturesSubscriberExtension implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(
        SortingService $sortingService,
        LocationServiceV2 $locationServiceV2,
        SystemConfigService $systemConfigService
    )
    {
        $this->sortingService = $sortingService;
        $this->locationServiceV2 = $locationServiceV2;
        $this->systemConfigService = $systemConfigService;
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

        if ($this->systemConfigService->get('MoorlMerchantFinder.config.merchantRadiusFilter')) {
            $filters->add($this->getRadiusFilter($request, $context));
        }
        if ($this->systemConfigService->get('MoorlMerchantFinder.config.merchantManufacturerFilter')) {
            $filters->add($this->getManufacturerFilter($request));
        }
        if ($this->systemConfigService->get('MoorlMerchantFinder.config.merchantCountryFilter')) {
            $filters->add($this->getCountryFilter($request));
        }
        if ($this->systemConfigService->get('MoorlMerchantFinder.config.merchantTagFilter')) {
            $filters->add($this->getTagFilter($request));
        }

        return $filters;
    }
}
