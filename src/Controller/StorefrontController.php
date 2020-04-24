<?php

namespace Moorl\MerchantFinder\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\Client;
use Moorl\MerchantFinder\MoorlMerchantFinder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
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

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $repository
    )
    {
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
    public function search(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $response = new JsonResponse();

        if ($data->get('action') == 'pick' && $data->get('merchant')) {
            $response->setData($this->setCustomerSession($data, $context));
        } else {
            $response->setData($this->searchProcess($data, $context->getContext()));
        }

        return $response;
    }

    protected function setCustomerSession($data, $context): array
    {
        if ($customer = $context->getCustomer()) {
            $customerRepo = $this->container->get('customer.repository');

            $customFields = $customer->getCustomFields() ?: [];
            $customFields['moorl_mf_merchant_id'] = $data->get('merchant');

            $customerRepo->update([[
                'id' => $customer->getId(),
                'customFields' => $customFields
            ]], $context->getContext());
        }

        $session = new Session();
        $session->set('moorl-merchant-finder_selected_merchant', $data->get('merchant'));

        /* unset to debug */
        if ($data->get('merchant') == 'b3bd48adbcb748e8a910f9155e0a8d05') {
            $session->remove('moorl-merchant-finder_selected_merchant');
        }

        return [
            'reload' => true,
        ];
    }

    protected function searchProcess($data, $context): array
    {
        $repo = $this->container->get('moorl_merchant.repository');
        $connection = $this->container->get(Connection::class);

        $data->set('distance', $data->get('distance') ?: '30');
        $data->set('items', (int)$data->get('items') ?: 500);

        $pluginConfig = $this->systemConfigService->getDomain('MoorlMerchantFinder.config');
        $filterCountries = !empty($pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes']) ? explode(',', $pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes']) : MoorlMerchantFinder::getDefault('allowedSearchCountryCodes');
        $searchEngine = !empty($pluginConfig['MoorlMerchantFinder.config.nominatim']) ? $pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes'] : MoorlMerchantFinder::getDefault('nominatim');


        $myLocation = [];

        if (!empty($data->get('zipcode'))) {
            $sql = <<<SQL
SELECT * FROM `moorl_zipcode`
WHERE `city` LIKE :city OR `zipcode` LIKE :zipcode
LIMIT 10; 
SQL;

            $myLocation = $connection->executeQuery($sql, [
                    'city' => '%' . $data->get('zipcode') . '%',
                    'zipcode' => '%' . $data->get('zipcode') . '%'
                ]
            )->fetchAll(FetchMode::ASSOCIATIVE);

            // No location found - Get them from OSM
            if (count($myLocation) == 0) {
                $query = http_build_query([
                    'q' => $data->get('zipcode'),
                    'format' => 'json',
                    'addressdetails' => 1,
                ]);

                $client = new Client();
                $res = $client->request('GET', $searchEngine . '?' . $query, ['headers' => ['Accept' => 'application/json', 'Content-type' => 'application/json']]);
                $resultData = json_decode($res->getBody()->getContents(), true);

                foreach ($resultData as $item) {
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

            $resultData = $connection->executeQuery($sql, [
                    'lat' => $myLocation[0]['lat'],
                    'lon' => $myLocation[0]['lon'],
                    'distance' => $data->get('distance'),
                ]
            )->fetchAll(FetchMode::ASSOCIATIVE);

            $merchantIds = [];
            $distance = [];

            foreach ($resultData as $item) {
                $merchantIds[] = $item['id'];
                $distance[$item['id']] = $item['distance'];
            }

            $criteria = new Criteria($merchantIds);
        } else {
            $criteria = new Criteria();
        }

        $criteria->setLimit($data->get('items'));
        $criteria->addAssociation('tags');
        $criteria->addAssociation('productManufacturers');
        $criteria->addAssociation('productManufacturers.media');
        $criteria->addAssociation('categories');
        $criteria->addAssociation('media');
        $criteria->addAssociation('marker');
        $criteria->addAssociation('markerShadow');
        $criteria->addFilter(new EqualsFilter('active', true));

        if ($data->get('tags')) {
            $criteria->addFilter(new EqualsFilter('tags.id', $data->get('tags')));
        }
        if ($data->get('productManufacturers')) {
            $criteria->addFilter(new EqualsFilter('productManufacturers.id', $data->get('productManufacturers')));
        }
        if ($data->get('categories')) {
            $criteria->addFilter(new EqualsFilter('categories.id', $data->get('categories')));
        }

        $criteria->setTerm($data->get('term'));

        $resultData = $this->repository->search($criteria, $context);

        if (isset($distance) && count($distance) > 0) {
            foreach ($resultData->getEntities() as $entity) {
                $entity->setDistance($distance[$entity->getId()]);
            }
        }

        return [
            'data' => $resultData->getEntities(),
            'loc' => $myLocation,
        ];
    }
}
