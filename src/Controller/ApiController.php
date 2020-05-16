<?php

namespace Moorl\MerchantFinder\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @RouteScope(scopes={"api"})
 */
class ApiController extends AbstractController
{
    private $genericLoader;
    private $systemConfigService;

    public function __construct(
        GenericPageLoader $genericLoader,
        SystemConfigService $systemConfigService
    )
    {
        $this->genericLoader = $genericLoader;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route("/api/v{version}/moorl/merchant-finder/import", name="api.moorl.merchant-finder.import", methods={"POST"}, requirements={"version"="\d+"})
     */
    public function import(Request $request, Context $context): JsonResponse
    {
        // TODO: make it happen
        return new JsonResponse();
    }

    /**
     * @Route("/api/v{version}/moorl/merchant-finder/update-locations", name="api.moorl.merchant-finder.update-locations", methods={"POST"}, requirements={"version"="\d+"})
     */
    public function updateLocations(Context $context): JsonResponse
    {
        // TODO: make it happen
        return new JsonResponse();
    }

    /**
     * @Route("/api/v{version}/moorl/merchant-finder/export", name="api.moorl.merchant-finder.export", methods={"GET"}, requirements={"version"="\d+"})
     */
    public function export(Context $context): void
    {
        $data = [];

        $mapping = [
            'id' => null,
            'originId' => null,
            'active' => null,
            'highlight' => null,
            'priority' => null,
            'company' => null,
            'department' => null,
            'vatId' => null,
            'email' => null,
            'firstName' => null,
            'lastName' => null,
            'zipcode' => null,
            'city' => null,
            'street' => null,
            'streetNumber' => null,
            'phoneNumber' => null,
            'country' => null,
            'countryCode' => null,
            'additionalAddressLine1' => null,
            'additionalAddressLine2' => null,
            'locationLat' => null,
            'locationLon' => null,
            'shopUrl' => null,
            'merchantUrl' => null,
            'description' => null,
            'openingHours' => null,
            'tags' => 'name',
            'categories' => 'id',
            'productManufacturers' => 'id',
            'media' => 'url',
            'marker' => 'url',
            'markerShadow' => 'url',
            'mediaId' => null,
            'markerId' => null,
            'markerShadowId' => null,
            /*'markerSettings' => null,*/
            'countryId' => null,
            'customerGroupId' => null,
            'salesChannelId' => null,
            'cmsPageId' => null,
        ];

        $repo = $this->container->get('moorl_merchant.repository');

        $criteria = new Criteria();
        $criteria->addAssociation('tags');
        $criteria->addAssociation('productManufacturers');
        $criteria->addAssociation('categories');
        $criteria->addAssociation('media');
        $criteria->addAssociation('marker');
        $criteria->addAssociation('markerShadow');
        $result = $repo->search($criteria, $context)->getEntities();

        foreach ($result as $entity) {
            $item = [];
            foreach ($mapping as $k => $v) {
                if ($v) {
                    $obj = $entity->get($k);
                    if ($obj instanceof EntityCollection) {
                        $item[$k] = implode("|", $obj->map(function(Entity $subEntity) use ($v) {
                            return $subEntity->get($v);
                        }));
                    }
                    if ($obj instanceof Entity) {
                        $item[$k] = $obj->get($v);
                    }
                    if ($obj == null) {
                        $item[$k] = null;
                    }
                } else {
                    $item[$k] = $entity->get($k);
                }
            }
            $data[] = $item;
        }

        $file = "MerchantFinder_export_" . date("Y-m-d-h-i-s") . ".csv";

        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$file);

        $out = fopen('php://output', 'w');

        fputcsv($out, array_keys($data[0]), ";");

        foreach ($data as $item) {
            fputcsv($out, array_values($item), ";");
        }

        fclose($out);
        die();
    }
}
