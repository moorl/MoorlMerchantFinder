<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\OpeningHour;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
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
    final public const ENTITY_NAME = 'moorl_merchant_oh';

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
            (new BoolField('repeat', 'repeat'))->addFlags(new EditField('switch')),
            (new JsonField('opening_hours', 'openingHours'))->addFlags(new EditField('opening-hours')),
            (new DateField('date', 'date'))->addFlags(new EditField('date')),
            (new DateField('show_from', 'showFrom'))->addFlags(new EditField('date')),
            (new DateField('show_until', 'showUntil'))->addFlags(new EditField('date')),
            (new StringField('title', 'title'))->addFlags(new EditField('text')),

            (new ManyToOneAssociationField('merchant', 'moorl_merchant_id', MerchantDefinition::class, 'id'))->addFlags(new EditField(), new LabelProperty('name')),
        ]);
    }
}
