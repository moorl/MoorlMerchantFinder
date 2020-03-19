<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Merchant;

use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Shopware\Core\System\Salutation\SalutationDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
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

    protected function defineFields(): FieldCollection
    {
        // TODO: Add Media & Brand manufacturerId
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new StringField('origin_id', 'originId'),
            new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class),
            new FkField('country_id', 'countryId', CountryDefinition::class),
            new FkField('customer_group_id', 'customerGroupId', CustomerGroupDefinition::class),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new FkField('marker_id', 'markerId', MediaDefinition::class),
            new FkField('marker_shadow_id', 'markerShadowId', MediaDefinition::class),
            new StringField('marker_settings', 'markerSettings'),
            new StringField('first_name', 'firstName'),
            new StringField('last_name', 'lastName'),
            new StringField('company', 'company'),
            new StringField('email', 'email'),
            new StringField('title', 'title'),
            new BoolField('active', 'active'),
            new StringField('zipcode', 'zipcode'),
            new StringField('city', 'city'),
            new StringField('street', 'street'),
            new StringField('street_number', 'streetNumber'),
            new StringField('country_code', 'countryCode'),
            new FloatField('location_lat','locationLat'),
            new FloatField('location_lon','locationLon'),
            new StringField('department', 'department'),
            new StringField('vat_id', 'vatId'),
            new StringField('phone_number', 'phoneNumber'),
            new StringField('description', 'description'),
            new StringField('opening_hours', 'openingHours'),
            new JsonField('data', 'data'),
            new StringField('additional_address_line1', 'additionalAddressLine1'),
            new StringField('additional_address_line2', 'additionalAddressLine2'),
            new StringField('shop_url', 'shopUrl'),
            new StringField('merchant_url', 'merchantUrl'),
            new CustomFields(),
            (new IntField('auto_increment', 'autoIncrement'))->addFlags(new WriteProtected()),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false),
            //new ManyToManyAssociationField('productManufacturer', 'product_manufacturer_id', ProductManufacturerDefinition::class, 'id', false),
            //new ManyToOneAssociationField('salutation', 'salutation_id', SalutationDefinition::class, 'id', false),
            (new ManyToManyAssociationField('categories', CategoryDefinition::class, MerchantCategoryDefinition::class, 'moorl_merchant_id', 'category_id'))->addFlags(new CascadeDelete(), new Inherited()),
            new ManyToManyAssociationField('tags', TagDefinition::class, MerchantTagDefinition::class, 'moorl_merchant_id', 'tag_id'),
            new ManyToManyAssociationField('productManufacturers', ProductManufacturerDefinition::class, MerchantProductManufacturerDefinition::class, 'moorl_merchant_id', 'product_manufacturer_id'),
        ]);
    }
}
