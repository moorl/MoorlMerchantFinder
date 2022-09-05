<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Controller;

use Moorl\MerchantFinder\Storefront\Page\Merchant\MerchantPageLoader;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MerchantController extends StorefrontController
{
    private MerchantPageLoader $merchantPageLoader;

    public function __construct(
        MerchantPageLoader $merchantPageLoader
    ) {
        $this->merchantPageLoader = $merchantPageLoader;
    }

    /**
     * @HttpCache()
     * @Route("/merchant/{merchantId}", name="moorl.merchant.detail", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function detail(SalesChannelContext $context, Request $request): Response
    {
        $page = $this->merchantPageLoader->load($request, $context);

        if (!$page->getCmsPage()) {
            return $this->renderStorefront('@Storefront/plugin/moorl-merchant-finder/page/merchant-detail/index.html.twig', [
                'page' => $page
            ]);
        }

        return $this->renderStorefront('@Storefront/plugin/moorl-merchant-finder/page/content/merchant-detail.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/merchant/{merchantId}/products", name="moorl.merchant.products", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function products(SalesChannelContext $context, Request $request): Response
    {
        $page = $this->merchantPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/element/cms-element-merchant-product-listing.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/merchant/{merchantId}/filter", name="moorl.merchant.filter", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function filter(SalesChannelContext $context, Request $request): JsonResponse
    {
       return new JsonResponse([]);
    }
}
