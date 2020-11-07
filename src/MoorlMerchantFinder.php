<?php declare(strict_types=1);

namespace Moorl\MerchantFinder;

use Doctrine\DBAL\Connection;
use Moorl\MerchantFinder\Core\Seo\MerchantSeoUrlRoute;
use MoorlFoundation\Core\PluginFoundation;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class MoorlMerchantFinder extends Plugin
{
    private static $_defaults = [
        'allowedSearchCountryCodes' => ['de', 'at', 'ch'],
        'nominatim' => 'https://nominatim.openstreetmap.org/search',
    ];

    public static function getDefault($key)
    {
        return static::$_defaults[$key];
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        /* @var $foundation PluginFoundation */
        $foundation = $this->container->get(PluginFoundation::class);
        $foundation->setContext($installContext->getContext());

        $data = [
            'routeName' => MerchantSeoUrlRoute::ROUTE_NAME,
            'entityName' => 'moorl_merchant',
            'template' => MerchantSeoUrlRoute::DEFAULT_TEMPLATE,
            'isValid' => true
        ];
        $foundation->addSeoUrlTemplate($data);
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        /* @var $foundation PluginFoundation */
        $foundation = $this->container->get(PluginFoundation::class);
        $foundation->setContext($context->getContext());

        $foundation->dropTables([
            'moorl_merchant_stock',
            'moorl_merchant_tag',
            'moorl_merchant_category',
            'moorl_merchant_product_manufacturer',
            'moorl_merchant_oh',
            'moorl_merchant_translation',
            'moorl_merchant',
            'moorl_zipcode'
        ]);

        //$foundation->executeQuery('ALTER TABLE `product` DROP COLUMN `MoorlMerchants`');
        $foundation->removePluginSnippets('moorl-merchant-finder');
        $foundation->removePluginConfig('MoorlMerchantFinder');
        $foundation->removeSeoUrlTemplate('moorl_merchant');
        $foundation->removeCmsBlocks(['moorl-merchant-finder-basic']);
    }
}
