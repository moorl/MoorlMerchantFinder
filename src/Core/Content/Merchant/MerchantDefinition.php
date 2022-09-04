<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantArea\MerchantAreaDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCategory\MerchantCategoryDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer\MerchantCustomerDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantProductManufacturer\MerchantProductManufacturerDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantSalesChannel\MerchantSalesChannelDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTag\MerchantTagDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTranslation\MerchantTranslationDefinition;
use Moorl\MerchantFinder\Core\Content\Marker\MarkerDefinition;
use MoorlFoundation\Core\Content\OpeningHours\OpeningHoursDefaults;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldCompanyCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldContactCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldCustomCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldOpeningHoursCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldThingCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\Unique;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldAddressCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldLocationCollection;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Collection\FieldPersonCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Shopware\Core\System\Tag\TagDefinition;

class MerchantDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'moorl_merchant';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MerchantCollection::class;
    }

    public function getEntityClass(): string
    {
        return MerchantEntity::class;
    }

    public function getDefaults(): array
    {
        return [
            'openingHours' => OpeningHoursDefaults::getOpeningHours(),
            'autoLocation' => false
        ];
    }

    protected function defineFields(): FieldCollection
    {
        $collection = new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new BoolField('is_default', 'isDefault'))->addFlags(new EditField('switch')),
            (new BoolField('delivery_active', 'deliveryActive'))->addFlags(new EditField('switch')),
            (new BoolField('collect_active', 'collectActive'))->addFlags(new EditField('switch')),
            (new BoolField('highlight', 'highlight'))->addFlags(new EditField('switch')),
            (new IntField('priority', 'priority'))->addFlags(new EditField('number')),
            (new StringField('origin_id', 'originId'))->addFlags(new EditField('text'), new Unique()),
            (new StringField('type', 'type'))->addFlags(new EditField('text')),
            (new StringField('delivery_type', 'deliveryType'))->addFlags(new EditField('text')),
            (new StringField('department', 'department'))->addFlags(new EditField('text')),
            (new StringField('country_code', 'countryCode'))->addFlags(new EditField('text')),
            (new StringField('shop_url', 'shopUrl'))->addFlags(new EditField('text')),
            (new StringField('merchant_url', 'merchantUrl'))->addFlags(new EditField('text')),
            (new TranslatedField('descriptionHtml'))->addFlags(new EditField('code')),
            new CustomFields(),
            new JsonField('data', 'data'),
            (new FloatField('delivery_price', 'deliveryPrice'))->addFlags(new EditField('number')),
            (new FloatField('min_order_value', 'minOrderValue'))->addFlags(new EditField('number')),
            new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class),
            new FkField('customer_group_id', 'customerGroupId', CustomerGroupDefinition::class),
            new FkField('moorl_merchant_marker_id', 'markerId', MarkerDefinition::class),
            (new ManyToOneAssociationField('marker', 'moorl_merchant_marker_id', MarkerDefinition::class, 'id', true))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false))->addFlags(),
            (new ManyToOneAssociationField('customerGroup', 'customer_group_id', CustomerGroupDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('categories', CategoryDefinition::class, MerchantCategoryDefinition::class, 'moorl_merchant_id', 'category_id'))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('tags', TagDefinition::class, MerchantTagDefinition::class, 'moorl_merchant_id', 'tag_id'))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('productManufacturers', ProductManufacturerDefinition::class, MerchantProductManufacturerDefinition::class, 'moorl_merchant_id', 'product_manufacturer_id'))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('products', ProductDefinition::class, MerchantStockDefinition::class, 'moorl_merchant_id', 'product_id'))->addFlags(new EditField(), new LabelProperty('productNumber')),
            (new ManyToManyAssociationField('salesChannels', SalesChannelDefinition::class, MerchantSalesChannelDefinition::class, 'moorl_merchant_id', 'sales_channel_id'))->addFlags(new EditField(), new LabelProperty('name')),
            new OneToManyAssociationField('merchantStocks', MerchantStockDefinition::class, 'moorl_merchant_id'),
            new OneToManyAssociationField('customers', MerchantCustomerDefinition::class, 'moorl_merchant_id'),
            new OneToManyAssociationField('merchantAreas', MerchantAreaDefinition::class, 'moorl_merchant_id'),
            new TranslationsAssociationField(MerchantTranslationDefinition::class, 'moorl_merchant_id'),
        ]);

        FieldThingCollection::merge($collection);
        FieldContactCollection::merge($collection);
        FieldPersonCollection::merge($collection);
        FieldAddressCollection::merge($collection);
        FieldCompanyCollection::merge($collection);
        FieldLocationCollection::merge($collection);
        FieldOpeningHoursCollection::merge($collection);
        FieldCustomCollection::merge($collection);

        return $collection;
    }
}
