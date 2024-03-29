<?php declare(strict_types=1);

namespace Moorl\MerchantFinder;

use Doctrine\DBAL\Connection;
use MoorlFoundation\Core\Service\DataService;
use MoorlFoundation\Core\Service\LocationServiceV2;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MoorlMerchantFinder extends Plugin
{
    use InheritanceUpdaterTrait;

    public const NAME = 'MoorlMerchantFinder';
    public const DATA_CREATED_AT = '2003-03-03 03:03:10.000';
    public const CMS_PAGE = 'moorl_merchant';
    public const CMS_PAGE_ID = 'd294117e81a9dee634b92f190d7719a3';
    public const CMS_PAGE_MERCHANT_DEFAULT_ID = 'fa05048f53068a5ec29e2c9aea4e8c40';
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
        'moorl_sorting',
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
    public const INHERITANCES = [
        'product' => ['MoorlMerchants', 'MoorlMerchantStocks'],
        'sales_channel' => ['MoorlMerchants'],
        'customer' => ['MoorlMerchants'],
        'order_line_item' => ['MoorlMerchantStock'],
    ];

    public function build(ContainerBuilder $container): void
    {
        if (class_exists(LocationServiceV2::class)) {
            parent::build($container);
        }
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        $this->updateInheritances();

        /* @var $dataService DataService */
        $dataService = $this->container->get(DataService::class);
        $dataService->install(self::NAME);
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);

        $this->updateInheritances();
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->uninstallTrait();
    }

    private function updateInheritances(): void
    {
        $connection = $this->container->get(Connection::class);

        foreach (self::INHERITANCES as $table => $propertyNames) {
            foreach ($propertyNames as $propertyName) {
                try {
                    $this->updateInheritance($connection, $table, $propertyName);
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }
    }

    private function uninstallTrait(): void
    {
        $connection = $this->container->get(Connection::class);

        foreach (self::PLUGIN_TABLES as $table) {
            $sql = sprintf('SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS `%s`;', $table);
            $connection->executeStatement ($sql);
        }

        foreach (array_reverse(self::SHOPWARE_TABLES) as $table) {
            $sql = sprintf("SET FOREIGN_KEY_CHECKS=0; DELETE FROM `%s` WHERE `created_at` = '%s';", $table, self::DATA_CREATED_AT);

            try {
                $connection->executeStatement ($sql);
            } catch (\Exception $exception) {
                continue;
            }
        }

        foreach (self::INHERITANCES as $table => $propertyNames) {
            foreach ($propertyNames as $propertyName) {
                $sql = sprintf("ALTER TABLE `%s` DROP `%s`;", $table, $propertyName);

                try {
                    $connection->executeStatement ($sql);
                } catch (\Exception $exception) {
                    continue;
                }
            }
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
