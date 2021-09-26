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

    public function filterByMerchantCustomerId(?string $customerId = null): self
    {
        return $this->filter(function (MerchantStockEntity $merchantStock) use ($customerId) {
            $merchant = $merchantStock->getMerchant();
            if (!$merchant || !$merchant->getCustomers()) {
                return false;
            }

            return $merchant->getCustomers()->filterByProperty('customerId', $customerId)->count() > 0;
        });
    }

    public function getByProductId(string $productId): ?MerchantStockEntity
    {
        return $this->filter(function (MerchantStockEntity $merchantStockEntity) use ($productId) {
            return $merchantStockEntity->getProductId() == $productId;
        })->first();
    }

    public function getByMerchantId(string $merchantId): ?MerchantStockEntity
    {
        return $this->filter(function (MerchantStockEntity $merchantStockEntity) use ($merchantId) {
            return $merchantStockEntity->getMerchantId() == $merchantId;
        })->first();
    }
}
