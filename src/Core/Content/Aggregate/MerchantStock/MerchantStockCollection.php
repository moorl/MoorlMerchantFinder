<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(MerchantStockEntity $entity)
 * @method void            set(string $key, MerchantStockEntity $entity)
 * @method MerchantStockEntity[]    getIterator()
 * @method MerchantStockEntity[]    getElements()
 * @method MerchantStockEntity|null get(string $key)
 * @method MerchantStockEntity|null first()
 * @method MerchantStockEntity|null last()
 */
class MerchantStockCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MerchantStockEntity::class;
    }
}
