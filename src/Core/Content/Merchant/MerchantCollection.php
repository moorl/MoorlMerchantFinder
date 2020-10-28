<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateCollection;
use Shopware\Core\System\Country\CountryCollection;

/**
 * @method void                       add(MerchantEntity $entity)
 * @method void                       set(string $key, MerchantEntity $entity)
 * @method MerchantEntity[]    getIterator()
 * @method MerchantEntity[]    getElements()
 * @method MerchantEntity|null get(string $key)
 * @method MerchantEntity|null first()
 * @method MerchantEntity|null last()
 */
class MerchantCollection extends EntityCollection
{

    public function getExport(): array
    {
        return $this->fmap(function (MerchantEntity $merchant) {
            return $merchant->getMediaId();
        });
    }

    public function getMediaIds(): array
    {
        return $this->fmap(function (MerchantEntity $merchant) {
            return $merchant->getMediaId();
        });
    }

    public function filterByMediaId(string $id): self
    {
        return $this->filter(function (MerchantEntity $merchant) use ($id) {
            return $merchant->getMediaId() === $id;
        });
    }

    protected function getExpectedClass(): string
    {
        return MerchantEntity::class;
    }
}
