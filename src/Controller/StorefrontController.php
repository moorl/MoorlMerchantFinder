<?php

namespace Moorl\MerchantFinder\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\Client;
use Moorl\MerchantFinder\Core\GeneralStruct;
use Moorl\MerchantFinder\MoorlMerchantFinder;
use Moorl\MerchantFinder\Service\MerchantService;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\Adapter\Twig\TemplateFinder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\ParameterBag;

use Shopware\Storefront\Controller\StorefrontController as OriginController;

/**
 * @RouteScope(scopes={"storefront"})
 */
class StorefrontController extends OriginController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $repository;
    private $systemConfigService;
    private $merchantService;
    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private $cmsPageLoader;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $repository,
        MerchantService $merchantService,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader
    )
    {
        $this->systemConfigService = $systemConfigService;
        $this->repository = $repository;
        $this->merchantService = $merchantService;
        $this->cmsPageLoader = $cmsPageLoader;
    }

    /**
     * @Route("/moorl/merchant-finder/suggest", name="moorl.merchant-finder.search", methods={"POST"}, defaults={"XmlHttpRequest"=true})
     */
    public function suggest(Request $request, RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        // TODO: Suggest Search, OSM doesn't allow service here
        return new JsonResponse();
    }

    /**
     * @Route("/moorl/merchant-finder/search", name="moorl.merchant-finder.search", methods={"POST"}, defaults={"XmlHttpRequest"=true})
     */
    public function search(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);

        if ($data->get('action') == 'pick' && $data->get('merchant')) {
            $response->setData($this->setCustomerSession($data, $context));
        } else if ($data->get('action') == 'unset') {
            $response->setData($this->setCustomerSession($data, $context));
        } else {
            $response->setData($this->searchProcess($data, $context));
        }

        return $response;
    }

    /**
     * @Route("/moorl/merchant-finder/unset", name="moorl.merchant-finder.unset", methods={"POST"}, defaults={"XmlHttpRequest"=true})
     */
    public function unset(RequestDataBag $data, SalesChannelContext $context): Response
    {
        $this->setCustomerSession($data, $context);

        return $this->redirectToRoute('frontend.home.page');
    }

    /**
     * @Route("/moorl/merchant-finder/merchant/{merchantId}", name="moorl.merchant-finder.merchant", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function merchant($merchantId, Request $request, SalesChannelContext $context): Response
    {
        $merchant = $this->merchantService->getMerchants($context->getContext(), new ParameterBag([
            'id' => $merchantId
        ]))->first();

        if ($merchant->getCmsPageId()) {
            $pages = $this->cmsPageLoader->load($request, new Criteria([$merchant->getCmsPageId()]), $context);

            if (!$pages->has($merchant->getCmsPageId())) {
                throw new PageNotFoundException($merchant->getCmsPageId());
            }

            $merchant->setCmsPage($pages->first());
        }

        return $this->renderStorefront('plugin/moorl-merchant-finder/page/merchant-detail.html.twig', [
            'merchant' => $merchant
        ]);
    }

    protected function setCustomerSession($data, $context): array
    {
        if ($data->get('action') == 'unset') {
            $merchantId = null;
        } else {
            $merchantId = $data->get('merchant');
        }

        if ($customer = $context->getCustomer()) {
            $customerRepo = $this->container->get('customer.repository');

            $customFields = $customer->getCustomFields() ?: [];
            $customFields['moorl_mf_merchant_id'] = $merchantId;

            $customerRepo->update([[
                'id' => $customer->getId(),
                'customFields' => $customFields
            ]], $context->getContext());
        }

        $session = new Session();
        $session->set('moorl-merchant-finder_selected_merchant', $merchantId);

        return [
            'reload' => true,
        ];
    }

    protected function searchProcess(RequestDataBag $data, SalesChannelContext $context): array
    {
        $merchants = $this->merchantService->getMerchants($context->getContext(), $data);

        $popupItemTemplate = null;
        $listItemTemplate = null;

        switch ($data->get('initiator')) {
            case 'merchant-picker':
                $listItemTemplate = $this->get(TemplateFinder::class)->find('plugin/moorl-merchant-picker/component/result-item-static.html.twig', false, null);
                break;
            default:
                $listItemTemplate = $this->get(TemplateFinder::class)->find('plugin/moorl-merchant-finder/component/result-item-static.html.twig', false, null);
                $popupItemTemplate = $this->get(TemplateFinder::class)->find('plugin/moorl-merchant-finder/component/popup-item-static.html.twig', false, null);
        }

        $html = '';
        $markers = [];

        foreach ($merchants as $merchant) {
            $html .= $this->renderView($listItemTemplate, ['merchant' => $merchant]);

            if ($popupItemTemplate) {
                $markers[] = [
                    'locationLat' => $merchant->getLocationLat(),
                    'locationLon' => $merchant->getLocationLon(),
                    'id' => $merchant->getId(),
                    'markerSettings' => $merchant->getMarkerSettings(),
                    'markerShadow' => $merchant->getMarkerShadow(),
                    'marker' => $merchant->getMarker(),
                    'popup' => $this->renderView($popupItemTemplate, ['merchant' => $merchant])
                ];
            }
        }

        //dump($markers); exit;

        if ($data->get('term') || $data->get('zipcode')) {
            $searchInfo = $this->trans('moorl-merchant-finder.forTheSearch');

            if ($data->get('term')) {
                $searchInfo .= '"' . $data->get('term') . '" ';
            }
            if ($data->get('zipcode')) {
                $searchInfo .= $this->trans('moorl-merchant-finder.forTheSearchZipcode', [
                    'zipcode' => $data->get('zipcode'),
                    'distance' => $data->get('distance')
                ]);
            }

            $searchInfo .= $this->trans('moorl-merchant-finder.are');
        } else {
            $searchInfo = $this->trans('moorl-merchant-finder.thereAre');
        }

        $searchInfo .= $this->trans('moorl-merchant-finder.resultsFound', [
            'count' => ($this->merchantService->getMerchantsCount() !== 0 ? $this->merchantService->getMerchantsCount() : $this->trans('moorl-merchant-finder.none')),
        ]);

        return [
            'data' => $markers,
            'html' => $html,
            'myLocation' => $this->merchantService->getMyLocation(),
            'searchInfo' => $searchInfo,
        ];
    }
}
