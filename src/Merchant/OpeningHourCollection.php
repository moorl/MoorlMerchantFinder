<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Merchant;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateCollection;
use Shopware\Core\System\Country\CountryCollection;

/**
 * @method void                       add(OpeningHourEntity $entity)
 * @method void                       set(string $key, OpeningHourEntity $entity)
 * @method OpeningHourEntity[]    getIterator()
 * @method OpeningHourEntity[]    getElements()
 * @method OpeningHourEntity|null get(string $key)
 * @method OpeningHourEntity|null first()
 * @method OpeningHourEntity|null last()
 */
class OpeningHourCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OpeningHourEntity::class;
    }
}
