<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\ElasticSearch;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Elasticsearch\Framework\AbstractElasticsearchDefinition;
use Shopware\Elasticsearch\Framework\Indexing\EntityMapper;

class ProductEsDefinitionDecorator extends AbstractElasticsearchDefinition
{
    public function __construct(private readonly AbstractElasticsearchDefinition $decorated, private readonly Connection $connection)
    {
    }

    public function getEntityDefinition(): EntityDefinition
    {
        return $this->decorated->getEntityDefinition();
    }

    public function extendEntities(EntityCollection $collection): EntityCollection
    {
        return $this->decorated->extendEntities($collection);
    }

    public function extendDocuments(array $documents, Context $context): array
    {
        return $this->decorated->extendDocuments($documents, $context);
    }

    public function getMapping(Context $context): array
    {
        $decoratedMapping = $this->decorated->getMapping($context);

        $decoratedMapping['properties']['MoorlMerchants'] = [
            'type' => 'nested',
            'properties' => [
                'id' => EntityMapper::KEYWORD_FIELD
            ],
        ];

        return $decoratedMapping;
    }

    public function fetch(array $ids, Context $context): array
    {
        $documents = $this->decorated->fetch($ids, $context);

        $parentIds = \array_filter(\array_column($documents, 'parentId'));

        $fetchingIds = \array_unique(\array_merge($parentIds, \array_map(fn($id) => Uuid::fromBytesToHex($id), $ids)));

        $merchants = $this->fetchCreators($fetchingIds);

        foreach ($documents as &$document) {
            $document['MoorlMerchants'] = [
                [
                    'id' => null
                ],
            ];

            if (isset($merchants[$document['id']])) {
                $merchantsItem = $merchants[$document['id']];

                $document['MoorlMerchants'] = \array_map(static fn($cpId) => [
                    'id' => $cpId
                ],
                    \array_column($merchantsItem, 'moorl_merchant_id')
                );
            }
        }
        unset($document);

        return $documents;
    }

    private function fetchCreators(array $productIds = []): array
    {
        $sql = <<<SQL
SELECT
    LOWER(HEX(moorl_merchant_stock.product_id)) as product_id,
    LOWER(HEX(moorl_merchant_stock.moorl_merchant_id)) as moorl_merchant_id
FROM moorl_merchant_stock
WHERE product_id in (?)
SQL;
        $data = $this->connection->fetchAllAssociative($sql, [Uuid::fromHexToBytesList($productIds)], [Connection::PARAM_STR_ARRAY]);

        return FetchModeHelper::group($data);
    }
}
