<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void            add(MerchantTranslationEntity $entity)
 * @method void            set(string $key, MerchantTranslationEntity $entity)
 * @method MerchantTranslationEntity[]    getIterator()
 * @method MerchantTranslationEntity[]    getElements()
 * @method MerchantTranslationEntity|null get(string $key)
 * @method MerchantTranslationEntity|null first()
 * @method MerchantTranslationEntity|null last()
 */
class MerchantTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MerchantTranslationEntity::class;
    }
}
