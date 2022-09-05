<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use MoorlFoundation\Core\System\EntityListingExtension;
use MoorlFoundation\Core\System\EntityListingInterface;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSuggestCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSuggestResultEvent;
use Shopware\Core\Content\Product\Events\ProductSearchResultEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestResultEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class MerchantListing extends EntityListingExtension implements EntityListingInterface
{
    public function getEntityName(): string
    {
        return MerchantDefinition::ENTITY_NAME;
    }

    public function getTitle(): string
    {
        return 'merchant-listing';
    }

    public function getSnippet(): ?string
    {
        return 'moorl-merchant-finder.merchants';
    }

    public function getElementConfig(): array
    {
        if ($this->isSearch() && $this->systemConfigService->get('MoorlMerchantFinder.config.searchConfigActive')) {
            return $this->systemConfigService->get('MoorlMerchantFinder.config.searchConfig') ?: parent::getElementConfig();
        } elseif ($this->isSuggest() && $this->systemConfigService->get('MoorlMerchantFinder.config.suggestConfigActive')) {
            return $this->systemConfigService->get('MoorlMerchantFinder.config.suggestConfig') ?: parent::getElementConfig();
        }

        return parent::getElementConfig();
    }

    public function isActive(): bool
    {
        if ($this->isSearch()) {
            return $this->systemConfigService->get('MoorlMerchantFinder.config.searchActive') ? true : false;
        } elseif ($this->isSuggest()) {
            return $this->systemConfigService->get('MoorlMerchantFinder.config.suggestActive') ? true : false;
        }

        return true;
    }

    public function getLimit(): int
    {
        if ($this->isSearch()) {
            return $this->systemConfigService->get('MoorlMerchantFinder.config.searchLimit') ?: 12;
        } elseif ($this->isSuggest()) {
            return $this->systemConfigService->get('MoorlMerchantFinder.config.suggestLimit') ?: 6;
        }

        return 1;
    }

    public function processCriteria(Criteria $criteria): void
    {
        $criteria->addAssociation('media');
        $criteria->addAssociation('country');
        $criteria->addFilter(new MerchantAvailableFilter($this->salesChannelContext));

        if ($this->event instanceof ProductSuggestResultEvent) {
            $eventClass = MerchantSuggestCriteriaEvent::class;
        } elseif ($this->event instanceof ProductSearchResultEvent) {
            $eventClass = MerchantSearchCriteriaEvent::class;
        } elseif ($this->isWidget()) {
            $eventClass = MerchantSearchCriteriaEvent::class;
        } else {
            $eventClass = MerchantListingCriteriaEvent::class;
        }

        $this->eventDispatcher->dispatch(
            new $eventClass($this->request, $criteria, $this->salesChannelContext)
        );
    }

    public function processSearchResult(ProductListingResult $searchResult): void
    {
        if ($this->event instanceof ProductSuggestResultEvent) {
            $eventClass = MerchantSuggestResultEvent::class;
        } elseif ($this->event instanceof ProductSearchResultEvent) {
            $eventClass = MerchantSearchResultEvent::class;
        } elseif ($this->isWidget()) {
            $eventClass = MerchantSearchResultEvent::class;
        } else {
            $eventClass = MerchantListingResultEvent::class;
        }

        $this->eventDispatcher->dispatch(
            new $eventClass($this->request, $searchResult, $this->salesChannelContext)
        );
    }
}
