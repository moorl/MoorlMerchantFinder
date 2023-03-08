<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Controller;

use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class MerchantSelectionController extends StorefrontController
{
    public function __construct(
        private readonly SalesChannelRepository $merchantRepository
    )
    {
    }

    #[Route(path: '/merchant-selection/modal', name: 'moorl.merchant-selection.modal', methods: ['GET'], defaults: ['XmlHttpRequest' => true])]
    public function selectionModal(SalesChannelContext $context, Request $request): Response
    {
        return $this->renderStorefront('@MoorlMerchantFinder/plugin/moorl-merchant-finder/component/selection-modal.html.twig', [
            'initiator' => $request->query->get('initiator', 'moorl-merchant-finder')
        ]);
    }

    #[Route(path: '/merchant-selection/search', name: 'moorl.merchant-selection.search', methods: ['GET','POST'], defaults: ['XmlHttpRequest' => true])]
    public function selectionSearch(SalesChannelContext $context, Request $request): Response
    {

    }

    #[Route(path: '/merchant-selection/pick', name: 'moorl.merchant-selection.pick', methods: ['GET','POST'], defaults: ['XmlHttpRequest' => true])]
    public function filter(SalesChannelContext $context, Request $request): JsonResponse
    {
       return new JsonResponse([]);
    }
}
