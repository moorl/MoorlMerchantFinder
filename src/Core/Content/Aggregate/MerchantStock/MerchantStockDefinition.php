<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldStockCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\DeliveryTime\DeliveryTimeDefinition;

class MerchantStockDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'moorl_merchant_stock';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return MerchantStockEntity::class;
    }

    public function getCollectionClass(): string
    {
        return MerchantStockCollection::class;
    }

    public function getDefaults(): array
    {
        return array_merge(
            [
                'isStock' => false
            ],
            FieldStockCollection::getDefaults()
        );
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(array_merge(
            [
                (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
                (new FkField('moorl_merchant_id', 'merchantId', MerchantDefinition::class))->addFlags(new Required()),
                (new ManyToOneAssociationField('merchant', 'moorl_merchant_id', MerchantDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),
                (new BoolField('is_stock', 'isStock'))->addFlags(new EditField('switch')),
                (new FkField('delivery_time_id', 'deliveryTimeId', DeliveryTimeDefinition::class)),
                (new ManyToOneAssociationField('deliveryTime', 'delivery_time_id', DeliveryTimeDefinition::class))->addFlags(new EditField(), new LabelProperty('name')),
            ],
            FieldStockCollection::getFieldItems()
        ));
    }
}
