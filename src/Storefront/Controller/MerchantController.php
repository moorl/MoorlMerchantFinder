<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Controller;

use Moorl\MerchantFinder\Storefront\Page\Merchant\MerchantPageLoader;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class MerchantController extends StorefrontController
{
    public function __construct(
        private readonly MerchantPageLoader $merchantPageLoader
    )
    {
    }

    #[Route(path: '/merchant/{merchantId}', name: 'frontend.moorl.merchant.detail', methods: ['GET'], defaults: ['XmlHttpRequest' => true])]
    public function detail(SalesChannelContext $context, Request $request): Response
    {
        $page = $this->merchantPageLoader->load($request, $context);

        if (!$page->getCmsPage()) {
            return $this->renderStorefront('@MoorlMerchantFinder/plugin/moorl-merchant-finder/page/merchant-detail/index.html.twig', [
                'page' => $page
            ]);
        }

        return $this->renderStorefront('@MoorlMerchantFinder/plugin/moorl-merchant-finder/page/content/merchant-detail.html.twig', [
            'page' => $page
        ]);
    }

    #[Route(path: '/merchant/{merchantId}/products', name: 'frontend.moorl.merchant.products', methods: ['GET'], defaults: ['XmlHttpRequest' => true])]
    public function products(SalesChannelContext $context, Request $request): Response
    {
        $page = $this->merchantPageLoader->load($request, $context);

        return $this->renderStorefront('@MoorlMerchantFinder/storefront/element/cms-element-merchant-product-listing.html.twig', [
            'page' => $page
        ]);
    }

    #[Route(path: '/merchant/{merchantId}/filter', name: 'frontend.moorl.merchant.filter', methods: ['GET'], defaults: ['XmlHttpRequest' => true])]
    public function filter(SalesChannelContext $context, Request $request): JsonResponse
    {
       return new JsonResponse([]);
    }
}
