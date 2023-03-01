<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantArea;

use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MerchantAreaDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'moorl_merchant_area';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MerchantAreaCollection::class;
    }

    public function getEntityClass(): string
    {
        return MerchantAreaEntity::class;
    }

    public function getDefaults(): array
    {
        return [
            'deliveryPrice' => 2,
            'minOrderValue' => 20,
            'deliveryTime' => 30,
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new FkField('moorl_merchant_id', 'merchantId', MerchantDefinition::class))->addFlags(new Required()),

            (new StringField('zipcode', 'zipcode'))->addFlags(new EditField('text'), new Required()),
            (new FloatField('delivery_price', 'deliveryPrice'))->addFlags(new EditField('number'), new Required()),
            (new FloatField('min_order_value', 'minOrderValue'))->addFlags(new EditField('number'), new Required()),
            (new IntField('delivery_time', 'deliveryTime'))->addFlags(new EditField('number'), new Required()),

            (new ManyToOneAssociationField('merchant', 'moorl_merchant_id', MerchantDefinition::class, 'id'))->addFlags(new EditField(), new LabelProperty('name')),
        ]);
    }
}
