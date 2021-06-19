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

    public function getMediaProperties(): array
    {
        $data = parent::getMediaProperties();
        $data[] = [
            'table' => 'moorl_merchant_marker',
            'mediaFolder' => 'cms_page',
            'properties' => [
                'markerId',
                'markerShadowId',
                'markerRetinaId'
            ]
        ];

        return $data;
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

    public function getInstallQueries(): array
    {
        return [
            "UPDATE `cms_page` SET `locked` = '1' WHERE `id` = UNHEX('{CMS_PAGE_ID}');"
        ];
    }
}
