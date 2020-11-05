<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\DeliveryTime\DeliveryTimeDefinition;

class MerchantStockDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'moorl_merchant_stock';

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

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),

            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new FkField('moorl_merchant_id', 'merchantId', MerchantDefinition::class))->addFlags(new Required()),
            (new FkField('delivery_time_id', 'deliveryTimeId', DeliveryTimeDefinition::class)),

            (new ManyToOneAssociationField('merchant', 'moorl_merchant_id', MerchantDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('productNumber')),
            (new ManyToOneAssociationField('deliveryTime', 'delivery_time_id', DeliveryTimeDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),

            (new BoolField('is_stock', 'isStock'))->addFlags(new EditField('switch')),
            (new IntField('stock', 'stock'))->addFlags(new EditField('number')),
        ]);
    }
}
