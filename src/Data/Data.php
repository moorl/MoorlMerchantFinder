<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Data;

use Moorl\MerchantFinder\MoorlMerchantFinder;
use MoorlFoundation\Core\System\DataExtension;
use MoorlFoundation\Core\System\DataInterface;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Doctrine\DBAL\Connection;

class Data extends DataExtension implements DataInterface
{
    private Connection $connection;
    private DefinitionInstanceRegistry $definitionInstanceRegistry;

    public function __construct(
        Connection $connection,
        DefinitionInstanceRegistry $definitionInstanceRegistry
    )
    {
        $this->connection = $connection;
        $this->definitionInstanceRegistry = $definitionInstanceRegistry;
    }

    public function getTables(): ?array
    {
        return array_merge(
            $this->getShopwareTables(),
            $this->getPluginTables()
        );
    }

    public function getShopwareTables(): ?array
    {
        return MoorlMerchantFinder::SHOPWARE_TABLES;
    }

    public function getPluginTables(): ?array
    {
        return MoorlMerchantFinder::PLUGIN_TABLES;
    }

    public function getPluginName(): string
    {
        return MoorlMerchantFinder::NAME;
    }

    public function getCreatedAt(): string
    {
        return MoorlMerchantFinder::DATA_CREATED_AT;
    }

    public function getLocalReplacers(): array
    {
        return [
            '{CMS_PAGE_ID}' => MoorlMerchantFinder::CMS_PAGE_ID
        ];
    }

    public function getName(): string
    {
        return 'data';
    }

    public function getType(): string
    {
        return 'data';
    }

    public function getPath(): string
    {
        return __DIR__;
    }

    public function getPreInstallQueries(): array
    {
        return [
            "DELETE FROM `seo_url_template` WHERE `route_name` = 'moorl.merchant-finder.merchant.page';"
        ];
    }

    public function getInstallQueries(): array
    {
        return [
            "INSERT IGNORE INTO `seo_url_template` (`id`,`is_valid`,`route_name`,`entity_name`,`template`) VALUES (UNHEX('{ID:WILD_0}'),1,'moorl.merchant.detail','moorl_merchant','merchant/{{ merchant.translated.name }}');"
        ];
    }
}
