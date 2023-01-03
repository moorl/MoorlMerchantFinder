<?php

namespace Moorl\MerchantFinder\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Moorl\MerchantFinder\Core\Service\MerchantService;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController as OriginController;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
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
    /**
     * @var GenericPageLoader
     */
    private $genericLoader;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $repository,
        MerchantService $merchantService,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        GenericPageLoader $genericLoader
    )
    {
        $this->systemConfigService = $systemConfigService;
        $this->repository = $repository;
        $this->merchantService = $merchantService;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->genericLoader = $genericLoader;
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
    public function search(Request $request, RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);

        if ($data->get('action') == 'pick' && $data->get('merchant')) {
            $response->setData($this->setCustomerSession($data, $context));
        } else if ($data->get('action') == 'unset') {
            $response->setData($this->setCustomerSession($data, $context));
        } else {
            $response->setData($this->searchProcess($request, $data, $context));
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
     * @Route("/moorl/merchant-finder/merchant/modal/{merchantId}", name="moorl.merchant-finder.merchant.modal", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function merchantModal($merchantId, Request $request, SalesChannelContext $context): Response
    {
        $merchant = $this->getMerchant($merchantId, $request, $context);

        $body = $this->renderView('plugin/moorl-merchant-finder/page/merchant-detail.html.twig', ['merchant' => $merchant]);

        return $this->renderStorefront('plugin/moorl-foundation/modal.html.twig', [
            'modal' => [
                'title' => $merchant->getTranslated()['name'] ?: $merchant->getCompany(),
                'size' => 'xl',
                'body' => $body
            ]
        ]);
    }

    /**
     * @Route("/moorl/merchant-finder/merchant/page/{merchantId}", name="moorl.merchant-finder.merchant.page", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function merchantPage($merchantId, Request $request, SalesChannelContext $context): Response
    {
        $page = $this->genericLoader->load($request, $context);

        return $this->renderStorefront('plugin/moorl-merchant-finder/page/merchant-detail-page.html.twig', [
            'merchant' => $this->getMerchant($merchantId, $request, $context),
            'page' => $page,
        ]);
    }

    protected function getMerchant(string $merchantId, Request $request, SalesChannelContext $context): ?MerchantEntity
    {
        $this->merchantService->setSalesChannelContext($context);

        /* @var $merchant MerchantEntity */
        $merchant = $this->merchantService->getMerchants(new ArrayStruct([
            'id' => $merchantId
        ]))->first();

        if (!$merchant) {
            return null;
        }

        if ($merchant->getCmsPageId()) {
            $pages = $this->cmsPageLoader->load($request, new Criteria([$merchant->getCmsPageId()]), $context);

            if (!$pages->has($merchant->getCmsPageId())) {
                throw new PageNotFoundException($merchant->getCmsPageId());
            }

            $merchant->setCmsPage($pages->first());
        }

        return $merchant;
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

    protected function searchProcess(Request $request, RequestDataBag $data, SalesChannelContext $context): array
    {
        $this->merchantService->setSalesChannelContext($context);

        $merchants = $this->merchantService->getMerchants();

        $popupItemTemplate = null;
        $listItemTemplate = null;

        switch ($data->get('initiator')) {
            case 'merchant-picker':
                $listItemTemplate = '@MoorlMerchantPicker/plugin/moorl-merchant-picker/component/result-item-static.html.twig';
                $popupItemTemplate = '@MoorlMerchantPicker/plugin/moorl-merchant-finder/component/popup-item-static.html.twig';
                break;
            case 'merchant-stock':
                $listItemTemplate = '@MoorlMerchantStock/plugin/moorl-merchant-stock/component/result-item-static.html.twig';
                $popupItemTemplate = '@MoorlMerchantStock/plugin/moorl-merchant-stock/component/popup-item-static.html.twig';
                break;
            default:
                $listItemTemplate = '@MoorlMerchantFinder/plugin/moorl-merchant-finder/component/result-item-static.html.twig';
                $popupItemTemplate = '@MoorlMerchantFinder/plugin/moorl-merchant-finder/component/popup-item-static.html.twig';
        }

        $html = '';
        $markers = [];

        /** @var MerchantEntity $merchant */
        foreach ($merchants as $merchant) {
            $html .= $this->renderView($listItemTemplate, ['merchant' => $merchant]);

            if ($popupItemTemplate) {
                $markers[] = [
                    'locationLat' => $merchant->getLocationLat(),
                    'locationLon' => $merchant->getLocationLon(),
                    'id' => $merchant->getId(),
                    /*'markerSettings' => $merchant->getMarker()->getMarkerSettings(),
                    'markerShadow' => $merchant->getMarker()->getMarkerShadow(),*/
                    'marker' => $merchant->getMarker(),
                    'popup' => $this->renderView($popupItemTemplate, ['merchant' => $merchant])
                ];
            }
        }

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

        $host = $request->attributes->get(RequestTransformer::SALES_CHANNEL_ABSOLUTE_BASE_URL) . $request->attributes->get(RequestTransformer::SALES_CHANNEL_BASE_URL);

        $seoUrlReplacer = $this->container->get(SeoUrlPlaceholderHandlerInterface::class);
        $html = $seoUrlReplacer->replace($html, $host, $context);

        return [
            'data' => $markers,
            'html' => $html,
            'myLocation' => $this->merchantService->getMyLocation(),
            'searchInfo' => $searchInfo,
        ];
    }
}
