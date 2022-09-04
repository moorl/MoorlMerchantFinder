<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Controller;

use MoorlCreator\Core\Content\Creator\SalesChannel\CreatorFollowRoute;
use MoorlCreator\Storefront\Page\Creator\CreatorPageLoader;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\LoginRequired;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MerchantController extends StorefrontController
{
    private CreatorPageLoader $creatorPageLoader;
    private CreatorFollowRoute $creatorFollowRoute;

    public function __construct(
        CreatorPageLoader $creatorPageLoader,
        CreatorFollowRoute $creatorFollowRoute
    ) {
        $this->creatorPageLoader = $creatorPageLoader;
        $this->creatorFollowRoute = $creatorFollowRoute;
    }

    /**
     * @HttpCache()
     * @Route("/creator/{creatorId}", name="moorl.creator.detail", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function detail(SalesChannelContext $context, Request $request): Response
    {
        $page = $this->creatorPageLoader->load($request, $context);

        if (!$page->getCmsPage()) {
            return $this->renderStorefront('@Storefront/plugin/moorl-creator/page/creator-detail/index.html.twig', [
                'page' => $page
            ]);
        }

        return $this->renderStorefront('@Storefront/plugin/moorl-creator/page/content/creator-detail.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @LoginRequired()
     * @Route("/creator/{creatorId}/follow", name="moorl.creator.follow", methods={"GET"})
     */
    public function follow(SalesChannelContext $context, Request $request): Response
    {
        $this->creatorFollowRoute->toggle($request, $context);

        return $this->redirectToRoute('moorl.creator.detail', [
            'creatorId' => $request->get('creatorId')
        ]);
    }

    /**
     * @Route("/creator/{creatorId}/products", name="moorl.creator.products", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function products(SalesChannelContext $context, Request $request): Response
    {
        $page = $this->creatorPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/element/cms-element-creator-product-listing.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/creator/{creatorId}/filter", name="moorl.creator.filter", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function filter(SalesChannelContext $context, Request $request): JsonResponse
    {
       return new JsonResponse([]);
    }
}
