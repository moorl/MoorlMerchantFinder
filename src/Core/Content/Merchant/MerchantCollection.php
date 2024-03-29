<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use MoorlFoundation\Core\Framework\DataAbstractionLayer\CollectionLocationTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

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
    use CollectionLocationTrait;

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

    public function sortByDistance(): self
    {
        $this->sort(function (MerchantEntity $a, MerchantEntity $b) {
            return $a->getDistance() > $b->getDistance();
        });

        return $this;
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
