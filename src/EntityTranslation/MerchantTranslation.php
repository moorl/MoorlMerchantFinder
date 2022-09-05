<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\EntityTranslation;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\System\EntityTranslationInterface;

class MerchantTranslation implements EntityTranslationInterface
{
    public function getConfigKey(): string
    {
        return 'MoorlMerchantFinder.config.translateMerchantProperties';
    }

    public function getEntityName(): string
    {
        return MerchantDefinition::ENTITY_NAME;
    }
}
