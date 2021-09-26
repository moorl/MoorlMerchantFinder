<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantArea;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(MerchantAreaEntity $entity)
 * @method void                set(string $key, MerchantAreaEntity $entity)
 * @method MerchantAreaEntity[]    getIterator()
 * @method MerchantAreaEntity[]    getElements()
 * @method MerchantAreaEntity|null get(string $key)
 * @method MerchantAreaEntity|null first()
 * @method MerchantAreaEntity|null last()
 */
class MerchantAreaCollection extends EntityCollection
{
    public function getByZipcode(string $zipcode): ?MerchantAreaEntity
    {
        foreach ($this->getElements() as $entity) {
            if ($entity->getZipcode() === $zipcode) {
                return $entity;
            }
        }
        return null;
    }

    public function getZipcodes(): array
    {
        $zipcodes = [];
        foreach ($this->getElements() as $entity) {
            $zipcodes[] = $entity->getZipcode();
        }
        return $zipcodes;
    }

    public function getApiAlias(): string
    {
        return 'moorl_merchant_area_collection';
    }

    protected function getExpectedClass(): string
    {
        return MerchantAreaEntity::class;
    }
}
