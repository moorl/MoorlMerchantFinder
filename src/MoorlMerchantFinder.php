<?php declare(strict_types=1);

namespace Moorl\MerchantFinder;

use Doctrine\DBAL\Connection;
use MoorlFoundation\Core\Service\DataService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class MoorlMerchantFinder extends Plugin
{
    public const NAME = 'MoorlMerchantFinder';
    public const DATA_CREATED_AT = '2003-03-03 03:03:10.000';
    public const CMS_PAGE = 'moorl_merchant';
    public const CMS_PAGE_ID = 'd294117e81a9dee634b92f190d7719a3';
    public const CMS_PAGE_MERCHANT_DEFAULT_ID = 'bf2b1ba2d91841c48e43b94d5aa84a9b';
    public const PLUGIN_TABLES = [
        'moorl_merchant',
        'moorl_zipcode',
        'moorl_merchant_category',
        'moorl_merchant_product_manufacturer',
        'moorl_merchant_tag',
        'moorl_merchant_oh',
        'moorl_merchant_translation',
        'moorl_merchant_stock',
        'moorl_merchant_customer',
        'moorl_merchant_marker',
        'moorl_merchant_area',
        'moorl_merchant_sales_channel'
    ];
    public const SHOPWARE_TABLES = [
        'media_folder',
        'cms_page',
        'cms_page_translation',
        'cms_section',
        'cms_block',
        'category',
        'category_translation',
        'product',
        'product_translation',
        'product_category',
        'product_visibility',
        'seo_url_template'
    ];

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        /* @var $dataService DataService */
        $dataService = $this->container->get(DataService::class);
        $dataService->install(self::NAME);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->removePluginData();
        $this->dropTables();
    }

    private function removePluginData(): void
    {
        $connection = $this->container->get(Connection::class);

        foreach (array_reverse(self::SHOPWARE_TABLES) as $table) {
            $sql = sprintf("SET FOREIGN_KEY_CHECKS=0; DELETE FROM `%s` WHERE `created_at` = '%s';", $table, self::DATA_CREATED_AT);

            try {
                $connection->executeUpdate($sql);
            } catch (\Exception $exception) {
                continue;
            }
        }
    }

    private function dropTables(): void
    {
        $connection = $this->container->get(Connection::class);

        foreach (self::PLUGIN_TABLES as $table) {
            $sql = sprintf('SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS `%s`;', $table);
            $connection->executeUpdate($sql);
        }
    }

    private static $_defaults = [
        'allowedSearchCountryCodes' => ['de', 'at', 'ch'],
        'nominatim' => 'https://nominatim.openstreetmap.org/search',
    ];

    public static function getDefault($key)
    {
        return static::$_defaults[$key];
    }
}
