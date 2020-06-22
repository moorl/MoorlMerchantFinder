<?php

namespace Moorl\MerchantFinder\Service;

use Moorl\MerchantFinder\MoorlMerchantFinder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\Client;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;

class MerchantService
{
    private $repository;
    private $systemConfigService;
    private $session;
    private $connection;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $repository,
        Connection $connection,
        Session $session
    )
    {
        $this->systemConfigService = $systemConfigService;
        $this->repository = $repository;
        $this->connection = $connection;
        $this->session = $session;
    }

    public function getLocationByTerm($term): array
    {
        if (!$term || empty($term)) {
            return [];
        }

        $pluginConfig = $this->systemConfigService->getDomain('MoorlMerchantFinder.config');
        $filterCountries = !empty($pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes']) ? explode(',', $pluginConfig['MoorlMerchantFinder.config.allowedSearchCountryCodes']) : MoorlMerchantFinder::getDefault('allowedSearchCountryCodes');
        $searchEngine = !empty($pluginConfig['MoorlMerchantFinder.config.nominatim']) ? $pluginConfig['MoorlMerchantFinder.config.nominatim'] : MoorlMerchantFinder::getDefault('nominatim');

        $sql = <<<SQL
SELECT * FROM `moorl_zipcode`
WHERE `city` LIKE :city OR `zipcode` LIKE :zipcode AND country_code IN (:countries)
LIMIT 10; 
SQL;

        $myLocation = $this->connection->executeQuery($sql, [
                'city' => '%' . $term . '%',
                'zipcode' => $term . '%',
                'countries' => implode(',', $filterCountries),
            ]
        )->fetchAll(FetchMode::ASSOCIATIVE);

        // No location found - Get them from OSM
        if (count($myLocation) == 0) {
            $queryString = implode(' ', [
                $term,
                count($filterCountries) == 1 ? current($filterCountries) : "",
            ]);

            $query = http_build_query([
                'q' => $queryString,
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
                        'state' => isset($item['address']['state']) ? $item['address']['state'] : null,
                        'country' => $item['address']['country'],
                        'country_code' => $item['address']['country_code'],
                        'suburb' => isset($item['address']['suburb']) ? $item['address']['suburb'] : null,
                        'lon' => $item['lon'],
                        'lat' => $item['lat'],
                        'licence' => $item['licence']
                    ];

                    $this->connection->executeQuery($sql, $placeholder);

                    $myLocation[] = $placeholder;
                }
            }
        }

        return $myLocation;
    }
}