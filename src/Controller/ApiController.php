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
        // TODO: clean export, maybe save as temp file & download
        $connection = $this->container->get(Connection::class);

        //$repo = $this->container->get('moorl_merchant.repository');
        //$criteria = new Criteria();
        //$data = $repo->search($criteria, $context);

        $sql = <<<SQL
SELECT 
    LOWER(HEX(`id`)) AS `id`,
    `origin_id` AS `originId`,
    LOWER(HEX(`sales_channel_id`)) AS `salesChannelId`,
    LOWER(HEX(`country_id`)) AS `countryId`,
    LOWER(HEX(`customer_group_id`)) AS `customerGroupId`,
    LOWER(HEX(`media_id`)) AS `mediaId`,
    LOWER(HEX(`marker_id`)) AS `markerId`,
    LOWER(HEX(`marker_shadow_id`)) AS `markerShadowId`,
    `company`,
    `first_name` AS `firstName`,
    `last_name` AS `lastName`,
    `email`,
    `zipcode`,
    `city`,
    `street`,
    `street_number` AS `streetNumber`,
    `country`,
    `country_code` AS `countryCode`,
    `location_lat` AS `locationLat`,
    `location_lon` AS `locationLon`,
    `vat_id` AS `vatId`,
    `phone_number` AS `phoneNumber`,
    `shop_url` AS `shopUrl`,
    `merchant_url` AS `merchantUrl`,
    `description`,
    `opening_hours` AS `openingHours`,
    `marker_settings` AS `markerSettings`
FROM `moorl_merchant`
ORDER BY `originId`;
SQL;

        $data = $connection->executeQuery($sql)->fetchAll(FetchMode::ASSOCIATIVE);

        $file = "MerchantFinder_export_" . date("Y-m-d-h-i-s") . ".csv";

        foreach ($data as &$item) {

            if (empty($item['originId'])) {

                $item['originId'] = $item['id'];

            }

            unset($item['id']);

        }

        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$file);

        $out = fopen('php://output', 'w');

        fputcsv($out, array_keys($data[0]));

        foreach ($data as $item) {
            fputcsv($out, array_values($item));
        }

        fclose($out);
        exit;

    }

}