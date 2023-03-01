<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Marker;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                       add(MarkerEntity $entity)
 * @method void                       set(string $key, MarkerEntity $entity)
 * @method MarkerEntity[]    getIterator()
 * @method MarkerEntity[]    getElements()
 * @method MarkerEntity|null get(string $key)
 * @method MarkerEntity|null first()
 * @method MarkerEntity|null last()
 */
class MarkerCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MarkerEntity::class;
    }

    public function getByType(string $type): ?MarkerEntity
    {
        return $this->filter(fn(MarkerEntity $markerEntity) => $markerEntity->getType() == $type)->first();
    }
}
