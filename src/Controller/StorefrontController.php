<?php

namespace Moorl\MerchantFinder\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\Client;
use Moorl\MerchantFinder\MoorlMerchantFinder;
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

class StorefrontController extends \Shopware\Storefront\Controller\StorefrontController
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
     * @Route("/moorl/merchant-finder", name="moorl.merchant-finder.index", options={"seo"=true}, methods={"GET"})
     */
    public function index(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        $page = $this->genericLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/moorl-merchant-finder/page/index.html.twig', [
            'pluginConfig' => $this->systemConfigService->getDomain('MoorlMerchantFinder.config'),
            'data' => $data,
            'page' => $page,
        ]);
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
        $pluginConfig = $this->systemConfigService->getDomain('MoorlMerchantFinder.config');

        $filterCountries = !empty($pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes']) ? explode(',', $pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes']) : MoorlMerchantFinder::getDefault('allowedSearchCountryCodes');

        $searchEngine = !empty($pluginConfig['MoorlMerchantFinder.config.nominatim']) ? $pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes'] : MoorlMerchantFinder::getDefault('nominatim');

        $connection = $this->container->get(Connection::class);

        $myLocation = [];

        if (!empty($request->request->get('zipcode'))) {

            $sql = <<<SQL
SELECT * FROM `moorl_zipcode`
WHERE `city` LIKE :city OR `zipcode` LIKE :zipcode
LIMIT 10; 
SQL;

            $myLocation = $connection->executeQuery($sql, [
                    'city' => '%' . $request->request->get('zipcode') . '%',
                    'zipcode' => '%' . $request->request->get('zipcode') . '%'
                ]
            )->fetchAll(FetchMode::ASSOCIATIVE);

            // No location found - Get them from OSM
            if (count($myLocation) == 0) {

                $query = http_build_query([
                    'q' => $request->request->get('zipcode'),
                    'format' => 'json',
                    'addressdetails' => 1,
                ]);

                $client = new Client();
                $res = $client->request('GET', $searchEngine . '?' . $query, ['headers' => ['Accept' => 'application/json', 'Content-type' => 'application/json']]);
                $data = json_decode($res->getBody()->getContents(), true);

                foreach ($data as $item) {

                    if (in_array($item['address']['country_code'], $filterCountries)) {

                        // Fill local database with locations
                        $sql = <<<SQL
INSERT IGNORE INTO `moorl_zipcode` (
    `id`,
    `zipcode`,
    `city`,
    `state`,
    `country`,
    `country_code`,
    `suburb`,
    `lon`,
    `lat`,
    `licence`
) VALUES (
    :id,
    :zipcode,
    :city,
    :state,
    :country,
    :country_code,
    :suburb,
    :lon,
    :lat,
    :licence
);
SQL;

                        $placeholder = [
                            'id' => $item['place_id'],
                            'zipcode' => isset($item['address']['postcode']) ? $item['address']['postcode'] : null,
                            'city' => isset($item['address']['city']) ? $item['address']['city'] : null,
                            'state' => $item['address']['state'],
                            'country' => $item['address']['country'],
                            'country_code' => $item['address']['country_code'],
                            'suburb' => isset($item['address']['suburb']) ? $item['address']['suburb'] : null,
                            'lon' => $item['lon'],
                            'lat' => $item['lat'],
                            'licence' => $item['licence']
                        ];

                        $connection->executeQuery($sql, $placeholder);

                        $myLocation[] = $placeholder;

                    }

                }

            }

        }

        if (count($myLocation) > 0) {

            $sql = <<<SQL
SELECT 
    LOWER(HEX(`id`)) AS `id`,
    LOWER(HEX(`sales_channel_id`)) AS `salesChannelId`,
    `origin_id` AS `originId`,
    `first_name` AS `firstName`,
    `last_name` AS `lastName`,
    `zipcode`,
    `city`,
    `company`,
    `street`,
    `street_number` AS `streetNumber`,
    `country`,
    `country_code` AS `countryCode`,
    `location_lat` AS `locationLat`,
    `location_lon` AS `locationLon`,
    `phone_number` AS `phoneNumber`,
    `shop_url` AS `shopUrl`,
    `merchant_url` AS `merchantUrl`,
    `description`,
    `opening_hours` AS `openingHours`,
    ACOS(
         SIN(RADIANS(:lat)) * SIN(RADIANS(`location_lat`)) 
         + COS(RADIANS(:lat)) * COS(RADIANS(`location_lat`))
         * COS(RADIANS(:lon) - RADIANS(`location_lon`))
    ) * 6380 AS distance
FROM `moorl_merchant`
WHERE CONCAT(`company`, `city`) LIKE :term
AND `active` IS TRUE
HAVING (`salesChannelId` = :salesChannelId OR `salesChannelId` IS NULL)
AND `distance` < :distance
ORDER BY `distance`
LIMIT 500;
SQL;

            $data = $connection->executeQuery($sql, [
                    'lat' => $myLocation[0]['lat'],
                    'lon' => $myLocation[0]['lon'],
                    'term' => '%' . $request->request->get('term') . '%',
                    'distance' => $request->request->get('distance'),
                    'salesChannelId' => $context->getSalesChannel()->getId(),
                ]
            )->fetchAll(FetchMode::ASSOCIATIVE);

        } else {

            $sql = <<<SQL
SELECT 
    LOWER(HEX(`id`)) AS `id`,
    LOWER(HEX(`sales_channel_id`)) AS `salesChannelId`,
    `origin_id` AS `originId`,
    `first_name` AS `firstName`,
    `last_name` AS `lastName`,
    `zipcode`,
    `city`,
    `company`,
    `street`,
    `street_number` AS `streetNumber`,
    `country`,
    `country_code` AS `countryCode`,
    `location_lat` AS `locationLat`,
    `location_lon` AS `locationLon`,
    `phone_number` AS `phoneNumber`,
    `shop_url` AS `shopUrl`,
    `merchant_url` AS `merchantUrl`,
    `description`,
    `opening_hours` AS `openingHours`
FROM `moorl_merchant`
WHERE CONCAT(`company`, `city`) LIKE :term 
AND `active` IS TRUE
HAVING `salesChannelId` = :salesChannelId OR `salesChannelId` IS NULL
ORDER BY `company`
LIMIT 500;
SQL;

            $data = $connection->executeQuery($sql, [
                    'term' => '%' . $request->request->get('term') . '%',
                    'salesChannelId' => $context->getSalesChannel()->getId(),
                ]
            )->fetchAll(FetchMode::ASSOCIATIVE);

        }

        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData([
            'data' => $data,
            'loc' => $myLocation,
        ]);

        return $response;

    }

}