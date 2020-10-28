<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTranslation;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MerchantTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'moorl_merchant_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MerchantTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return MerchantTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return MerchantDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new StringField('name', 'name'),
            new LongTextField('description', 'description'),
            (new LongTextField('description_html', 'descriptionHtml'))->addFlags(new AllowHtml()),
            new LongTextField('opening_hours', 'openingHours'),
        ]);
    }
}
