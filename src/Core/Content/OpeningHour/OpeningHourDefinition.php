<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\OpeningHour;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OpeningHourDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'moorl_merchant_oh';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return OpeningHourCollection::class;
    }

    public function getEntityClass(): string
    {
        return OpeningHourEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new FkField('moorl_merchant_id', 'merchantId', MerchantDefinition::class),
            new BoolField('repeat', 'repeat'),
            new JsonField('opening_hours', 'openingHours'),
            new DateField('date', 'date'),
            new DateField('show_from', 'showFrom'),
            new DateField('show_until', 'showUntil'),
            new StringField('title', 'title'),
            new ManyToOneAssociationField('merchant', 'moorl_merchant_id', MerchantDefinition::class, 'id'),
        ]);
    }
}
