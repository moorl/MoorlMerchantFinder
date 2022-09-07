<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Listing;

use Doctrine\DBAL\Connection;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantListingResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchCriteriaEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSearchResultEvent;
use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\Events\MerchantSuggestCriteriaEvent;
use Moorl\MerchantFinder\GeoLocation\GeoPoint;
use MoorlFoundation\Core\Content\Sorting\SortingCollection;
use MoorlFoundation\Core\Service\LocationService;
use MoorlFoundation\Core\Service\SortingService;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter;
use Shopware\Core\Content\Product\SalesChannel\Listing\FilterCollection;
use Shopware\Core\Content\Product\SalesChannel\Sorting\ProductSortingEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\FilterAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\SumAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class MerchantListingFeaturesSubscriber implements EventSubscriberInterface
{
    public const DEFAULT_SEARCH_SORT = 'standard';

    private SortingService $sortingService;
    private LocationService $locationService;

    public function __construct(
        SortingService $sortingService,
        LocationService $locationService
    )
    {
        $this->sortingService = $sortingService;
        $this->locationService = $locationService;
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

    public function handleFlags(ProductListingCriteriaEvent $event): void
    {
        $request = $event->getRequest();
        $criteria = $event->getCriteria();

        if ($request->get('no-aggregations')) {
            $criteria->resetAggregations();
        }

        if ($request->get('only-aggregations')) {
            $criteria->setLimit(0);
            $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_NONE);
            $criteria->resetSorting();
            $criteria->resetAssociations();
        }
    }

    public function handleListingRequest(ProductListingCriteriaEvent $event): void
    {
        $request = $event->getRequest();
        $criteria = $event->getCriteria();
        $context = $event->getSalesChannelContext();

        if (!$request->get('order')) {
            $request->request->set('order', self::DEFAULT_SEARCH_SORT);
        }

        $this->handlePagination($request, $criteria, $event->getSalesChannelContext());
        $this->handleFilters($request, $criteria, $context);
        $this->handleSorting($request, $criteria, $context);
    }

    public function handleSearchRequest(ProductListingCriteriaEvent $event): void
    {
        $request = $event->getRequest();
        $criteria = $event->getCriteria();
        $context = $event->getSalesChannelContext();

        if (!$request->get('order')) {
            $request->request->set('order', self::DEFAULT_SEARCH_SORT);
        }

        $this->handlePagination($request, $criteria, $event->getSalesChannelContext());
        $this->handleFilters($request, $criteria, $context);
        $this->handleSorting($request, $criteria, $context);
    }

    public function handleResult(ProductListingResultEvent $event): void
    {
        $this->addCurrentFilters($event);

        $result = $event->getResult();

        $sortings = $this->sortingService->getAvailableSortings(
            MerchantDefinition::ENTITY_NAME,
            $event->getSalesChannelContext()->getContext()
        );

        $currentSortingKey = $this->getCurrentSorting($sortings, $event->getRequest())->getKey();

        $result->setSorting($currentSortingKey);
        $result->setAvailableSortings($sortings);
        $result->setPage($this->getPage($event->getRequest()));
        $result->setLimit($this->getLimit($event->getRequest()));
    }

    private function handleFilters(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {
        $filters = $this->getFilters($request, $context);

        $aggregations = $this->getAggregations($request, $filters);

        foreach ($aggregations as $aggregation) {
            $criteria->addAggregation($aggregation);
        }

        if ($request->get('search')) {
            $criteria->setTerm($request->get('search'));
        }

        foreach ($filters as $filter) {
            if ($filter->isFiltered()) {
                $criteria->addPostFilter($filter->getFilter());
            }
        }

        $criteria->addExtension('filters', $filters);
    }

    private function getAggregations(Request $request, FilterCollection $filters): array
    {
        $aggregations = [];

        if ($request->get('reduce-aggregations') === null) {
            foreach ($filters as $filter) {
                $aggregations = array_merge($aggregations, $filter->getAggregations());
            }

            return $aggregations;
        }

        foreach ($filters as $filter) {
            $excluded = $filters->filtered();

            if ($filter->exclude()) {
                $excluded = $excluded->blacklist($filter->getName());
            }

            foreach ($filter->getAggregations() as $aggregation) {
                if ($aggregation instanceof FilterAggregation) {
                    $aggregation->addFilters($excluded->getFilters());

                    $aggregations[] = $aggregation;

                    continue;
                }

                $aggregation = new FilterAggregation(
                    $aggregation->getName(),
                    $aggregation,
                    $excluded->getFilters()
                );

                $aggregations[] = $aggregation;
            }
        }

        return $aggregations;
    }

    private function handlePagination(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {
        $limit = $this->getLimit($request);
        $page = $this->getPage($request);
        $criteria->setOffset(($page - 1) * $limit);
        $criteria->setLimit($limit);
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
    }

    private function handleSorting(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {
        if ($criteria->getSorting()) {
            return;
        }

        $sortings = $this->sortingService->getAvailableSortings(
            MerchantDefinition::ENTITY_NAME,
            $context->getContext()
        );
        $currentSorting = $this->getCurrentSorting($sortings, $request);
        $criteria->addSorting(...$currentSorting->createDalSorting());
        $criteria->addExtension('sortings', $sortings);
    }

    private function getCurrentSorting(SortingCollection $sortings, Request $request): ProductSortingEntity
    {
        $key = $request->get('order');
        $sorting = $sortings->getByKey($key);
        if ($sorting !== null) {
            return $sorting;
        }
        return $sortings->first();
    }

    private function addCurrentFilters(ProductListingResultEvent $event): void
    {
        $result = $event->getResult();
        $filters = $result->getCriteria()->getExtension('filters');
        if (!$filters instanceof FilterCollection) {
            return;
        }
        foreach ($filters as $filter) {
            $result->addCurrentFilter($filter->getName(), $filter->getValues());
        }
    }

    private function getLimit(Request $request): int
    {
        $limit = $request->query->getInt('limit', 0);
        if ($request->isMethod(Request::METHOD_POST)) {
            $limit = $request->request->getInt('limit', $limit);
        }
        return $limit <= 0 ? 12 : $limit;
    }

    private function getPage(Request $request): int
    {
        $page = $request->query->getInt('p', 1);
        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('p', $page);
        }
        return $page <= 0 ? 1 : $page;
    }

    private function getFilters(Request $request, SalesChannelContext $context): FilterCollection
    {
        $filters = new FilterCollection();

        $filters->add($this->getRadiusFilter($request));
        $filters->add($this->getManufacturerFilter($request));
        $filters->add($this->getCountryFilter($request));
        $filters->add($this->getTagFilter($request));

        //dump($filters);exit;

        return $filters;
    }

    private function getTagFilter(Request $request): Filter
    {
        $ids = $this->getTagIds($request);

        return new Filter(
            'tag',
            !empty($ids),
            [new EntityAggregation('tag', 'moorl_merchant.tags.id', 'tag')],
            new EqualsAnyFilter('moorl_merchant.tags.id', $ids),
            $ids
        );
    }

    private function getManufacturerFilter(Request $request): Filter
    {
        $ids = $this->getManufacturerIds($request);

        return new Filter(
            'manufacturer',
            !empty($ids),
            [new EntityAggregation('manufacturer', 'moorl_merchant.productManufacturers.id', 'product_manufacturer')],
            new EqualsAnyFilter('moorl_merchant.productManufacturers.id', $ids),
            $ids
        );
    }

    private function getCountryFilter(Request $request): Filter
    {
        $ids = $this->getCountryIds($request);

        return new Filter(
            'country',
            !empty($ids),
            [new EntityAggregation('country', 'moorl_merchant.countryId', 'country')],
            new EqualsAnyFilter('moorl_merchant.countryId', $ids),
            $ids
        );
    }

    private function getGeoPoint(): ?GeoPoint
    {
        return null;
    }

    private function getRadiusFilter(Request $request): Filter
    {
        /**
         * km = Kilometer
         * mi = Meilen
         * nm = Nautische Meilen
         */

        $location = $request->get('location', '');
        $distance = $request->get('distance', 0);
        $unit = $request->get('unit', 'km');

        $filter = new EqualsFilter('moorl_merchant.active', true);

        $geoPoint = $this->locationService->getLocationByTerm($location);
        if ($geoPoint) {
            $boundingBox = $geoPoint->boundingBox($distance, $unit);

            $filter = new MultiFilter(MultiFilter::CONNECTION_AND, [
                new RangeFilter('moorl_merchant.locationLat', [
                    'gte' => $boundingBox->getMinLatitude(),
                    'lte' => $boundingBox->getMaxLatitude()
                ]),
                new RangeFilter('moorl_merchant.locationLon', [
                    'lte' => $boundingBox->getMaxLongitude(),
                    'gte' => $boundingBox->getMinLongitude()
                ])
            ]);
        }

        return new Filter(
            'radius',
            !empty($geoPoint),
            [new CountAggregation('radius', 'moorl_merchant.active')],
            $filter,
            [
                'location' => $location,
                'distance' => (int) $distance,
                'unit' => $unit,
                'locationLat' => !empty($geoPoint) ? $geoPoint->getDegLat() : null,
                'locationLon' => !empty($geoPoint) ? $geoPoint->getDegLon() : null
            ]
        );
    }

    private function getManufacturerIds(Request $request): array
    {
        $ids = $request->query->get('manufacturer', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->request->get('manufacturer', '');
        }

        if (\is_string($ids)) {
            $ids = explode('|', $ids);
        }

        return array_filter((array) $ids);
    }

    private function getCountryIds(Request $request): array
    {
        $ids = $request->query->get('country', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->request->get('country', '');
        }

        if (\is_string($ids)) {
            $ids = explode('|', $ids);
        }

        return array_filter((array) $ids);
    }

    private function getTagIds(Request $request): array
    {
        $ids = $request->query->get('tag', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->request->get('tag', '');
        }

        if (\is_string($ids)) {
            $ids = explode('|', $ids);
        }

        return array_filter((array) $ids);
    }
}
