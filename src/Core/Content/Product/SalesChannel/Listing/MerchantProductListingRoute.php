<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Product\SalesChannel\Listing;

use Moorl\MerchantFinder\MoorlMerchantFinder;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\AbstractProductListingRoute;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingLoader;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingRouteResponse;
use Shopware\Core\Content\Product\SalesChannel\ProductAvailableFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class MerchantProductListingRoute extends AbstractProductListingRoute
{
    public function __construct(
        private readonly AbstractProductListingRoute $decorated,
        private readonly ProductListingLoader $listingLoader,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function getDecorated(): AbstractProductListingRoute
    {
        return $this->decorated;
    }

    public function load(string $categoryId, Request $request, SalesChannelContext $context, Criteria $criteria): ProductListingRouteResponse
    {
        if ($categoryId === MoorlMerchantFinder::NAME) {
            $this->eventDispatcher->dispatch(
                new ProductListingCriteriaEvent($request, $criteria, $context)
            );

            $criteria->addFilter(
                new ProductAvailableFilter($context->getSalesChannel()->getId(), ProductVisibilityDefinition::VISIBILITY_ALL)
            );

            $criteria->setLimit(24);

            $entities = $this->listingLoader->load($criteria, $context);

            $result = ProductListingResult::createFrom($entities);

            $this->eventDispatcher->dispatch(
                new ProductListingResultEvent($request, $result, $context)
            );

            return new ProductListingRouteResponse($result);
        }

        return $this->decorated->load($categoryId, $request, $context, $criteria);
    }
}
