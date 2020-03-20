<?php

namespace Moorl\MerchantFinder\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\Client;
use Moorl\MerchantFinder\MoorlMerchantFinder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
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
 * @RouteScope(scopes={"storefront"})
 */
class StorefrontController extends \Shopware\Storefront\Controller\StorefrontController
{

    /**
     * @var EntityRepositoryInterface
     */
    private $repository;
    private $genericLoader;
    private $systemConfigService;

    public function __construct(
        GenericPageLoader $genericLoader,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $repository
    )
    {
        $this->genericLoader = $genericLoader;
        $this->systemConfigService = $systemConfigService;
        $this->repository = $repository;
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
    ACOS(
         SIN(RADIANS(:lat)) * SIN(RADIANS(`location_lat`)) 
         + COS(RADIANS(:lat)) * COS(RADIANS(`location_lat`))
         * COS(RADIANS(:lon) - RADIANS(`location_lon`))
    ) * 6380 AS distance
FROM `moorl_merchant`
WHERE `active` IS TRUE
HAVING `distance` < :distance
ORDER BY `distance`
LIMIT 500;
SQL;

            $data = $connection->executeQuery($sql, [
                    'lat' => $myLocation[0]['lat'],
                    'lon' => $myLocation[0]['lon'],
                    'distance' => $request->request->get('distance')
                ]
            )->fetchAll(FetchMode::ASSOCIATIVE);

            $merchantIds = [];
            $distance = [];

            foreach ($data as $item) {
                $merchantIds[] = $item['id'];
                $distance[$item['id']] = $item['distance'];
            }

            $criteria = new Criteria($merchantIds);

        } else {

            $criteria = new Criteria();

        }

        $criteria->addAssociation('tags');
        $criteria->addAssociation('productManufacturers');
        $criteria->addAssociation('productManufacturers.media');
        $criteria->addAssociation('categories');
        $criteria->addAssociation('media');
        $criteria->addFilter(new EqualsFilter('active', true));

        if ($request->request->get('tags')) {
            $criteria->addFilter(new EqualsFilter('tags.id', $request->request->get('tags')));
        }
        if ($request->request->get('productManufacturers')) {
            $criteria->addFilter(new EqualsFilter('productManufacturers.id', $request->request->get('productManufacturers')));
        }
        if ($request->request->get('categories')) {
            $criteria->addFilter(new EqualsFilter('categories.id', $request->request->get('categories')));
        }

        $criteria->setTerm($request->request->get('term'));

        $result = $this->repository->search($criteria, $context->getContext());

        if (isset($distance)) {
            foreach ($result->getEntities() as $entity) {
                $entity->setDistance($distance[$entity->getId()]);
            }
        }

        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData([
            'data' => $result->getEntities(),
            'loc' => $myLocation,
        ]);

        return $response;

    }

}
