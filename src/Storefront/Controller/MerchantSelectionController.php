<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Controller;

use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\SalesChannelMerchantEntity;
use Shopware\Core\Content\Product\SalesChannel\Listing\AbstractProductListingRoute;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class MerchantSelectionController extends StorefrontController
{
    public function __construct(
        private readonly AbstractProductListingRoute $listingRoute,
        private readonly SystemConfigService $systemConfigService
    )
    {
    }

    #[Route(path: '/merchant-selection/modal', name: 'moorl.merchant-selection.modal', methods: ['GET'], defaults: ['XmlHttpRequest' => true])]
    public function selectionModal(SalesChannelContext $salesChannelContext, Request $request): Response
    {
        $initiator = $request->query->get('initiator', 'moorl-merchant-finder');

        return $this->renderStorefront('@MoorlMerchantFinder/plugin/moorl-merchant-finder/component/merchant-selection/selection-modal.html.twig', [
            'initiator' => $initiator,
            'listing' => $this->getListing($initiator, $salesChannelContext, $request),
        ]);
    }

    #[Route(path: '/merchant-selection/search', name: 'moorl.merchant-selection.search', methods: ['GET','POST'], defaults: ['XmlHttpRequest' => true])]
    public function selectionSearch(SalesChannelContext $salesChannelContext, Request $request): Response
    {
        $initiator = $request->query->get('initiator', 'moorl-merchant-finder');

        return $this->renderStorefront('@MoorlMerchantFinder/plugin/moorl-merchant-finder/component/merchant-selection/selection-listing.html.twig', [
            'initiator' => $initiator,
            'listing' => $this->getListing($initiator, $salesChannelContext, $request),
        ]);
    }

    #[Route(path: '/merchant-selection/pick', name: 'moorl.merchant-selection.pick', methods: ['GET','POST'], defaults: ['XmlHttpRequest' => true])]
    public function selectionPick(SalesChannelContext $salesChannelContext, Request $request): JsonResponse
    {
        $initiator = $request->query->get('initiator', 'moorl-merchant-finder');

        $cookie = Cookie::create(
            $initiator,
            $request->query->get('merchantId')
        );
        $cookie->setSecureDefault($request->isSecure());
        $cookie = $cookie->withExpires(time() + 60 * 60 * 24 * 30);
        $cookie = $cookie->withHttpOnly(false);

        $response = new JsonResponse([
            'reload' => true
        ]);
        $response->headers->setCookie($cookie);

        return $response;
    }

    #[Route(path: '/merchant-selection/name', name: 'moorl.merchant-selection.name', methods: ['GET','POST'], defaults: ['XmlHttpRequest' => true])]
    public function selectionName(SalesChannelContext $salesChannelContext, Request $request): Response
    {
        $initiator = $request->query->get('initiator', 'moorl-merchant-finder');

        $merchantId = $request->cookies->get($initiator);
        $merchant = $this->getItem($merchantId, $initiator, $salesChannelContext, $request);

        return new Response(sprintf(
            "%s | %s",
            $merchant->getTranslation('name'),
            $merchant->getCity()
        ));
    }

    private function getListing(string $initiator, SalesChannelContext $salesChannelContext, Request $request): ProductListingResult
    {
        $request->query->set('tab', 'merchant-listing');
        $request->query->set('order', 'score');
        $request->query->set(
            'distance',
            $this->systemConfigService->get('MoorlMerchantFinder.config.merchantSelectionRadius', $salesChannelContext->getSalesChannelId()) ?: 30
        );

        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addAssociation('country');
        $criteria->setLimit(
            $this->systemConfigService->get('MoorlMerchantFinder.config.merchantSelectionLimit', $salesChannelContext->getSalesChannelId()) ?: 20
        );

        return $this->listingRoute
            ->load($initiator, $request, $salesChannelContext, $criteria)
            ->getResult();
    }

    private function getItem(string $merchantId, string $initiator, SalesChannelContext $salesChannelContext, Request $request): ?SalesChannelMerchantEntity
    {
        $request->query->set('tab', 'merchant-listing');
        $request->query->set('order', 'score');

        $criteria = new Criteria([$merchantId]);
        $criteria->addAssociation('media');
        $criteria->addAssociation('country');
        $criteria->setLimit(1);

        return $this->listingRoute
            ->load($initiator, $request, $salesChannelContext, $criteria)
            ->getResult()
            ->first();
    }
}
