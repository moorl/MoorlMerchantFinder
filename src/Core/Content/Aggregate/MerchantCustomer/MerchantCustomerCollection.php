<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(MerchantCustomerEntity $entity)
 * @method void            set(string $key, MerchantCustomerEntity $entity)
 * @method MerchantCustomerEntity[]    getIterator()
 * @method MerchantCustomerEntity[]    getElements()
 * @method MerchantCustomerEntity|null get(string $key)
 * @method MerchantCustomerEntity|null first()
 * @method MerchantCustomerEntity|null last()
 */
class MerchantCustomerCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MerchantCustomerEntity::class;
    }

    public function filterByMerchantCustomerId(string $customerId): self
    {
        return $this->filter(function (MerchantCustomerEntity $MerchantCustomer) use ($customerId) {
            $merchant = $MerchantCustomer->getMerchant();
            if (!$merchant || !$merchant->getCustomers()) {
                return false;
            }

            return $merchant->getCustomers()->filterByProperty('customerId', $customerId)->count() > 0;
            //return $merchant->getCustomers()->has($customerId);
        });
    }
}
