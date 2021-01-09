<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTranslation\MerchantTranslationDefinition;
use Moorl\MerchantFinder\Core\Content\Marker\MarkerDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\DistanceField;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCategory\MerchantCategoryDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantProductManufacturer\MerchantProductManufacturerDefinition;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTag\MerchantTagDefinition;
use Moorl\MerchantFinder\Core\Content\OpeningHour\OpeningHourDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\Unique;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer\MerchantCustomerDefinition;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
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
use Shopware\Core\System\Country\CountryDefinition;
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

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new StringField('origin_id', 'originId'))->addFlags(new EditField('text'), new Unique()),
            (new BoolField('active', 'active'))->addFlags(new EditField('switch')),
            (new IntField('priority', 'priority'))->addFlags(new EditField('number')),
            (new BoolField('highlight', 'highlight'))->addFlags(new EditField('switch')),
            (new StringField('type', 'type'))->addFlags(new EditField('text')),
            (new StringField('custom1', 'custom1'))->addFlags(new EditField('text')),
            (new StringField('custom2', 'custom2'))->addFlags(new EditField('text')),
            (new StringField('custom3', 'custom3'))->addFlags(new EditField('text')),
            (new StringField('custom4', 'custom4'))->addFlags(new EditField('text')),

            (new TranslatedField('name'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new EditField('text')),
            (new TranslatedField('description'))->addFlags(new EditField('textarea')),
            (new TranslatedField('descriptionHtml'))->addFlags(new EditField('code')),
            (new TranslatedField('openingHours'))->addFlags(new EditField('textarea')),

            new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class),
            new FkField('country_id', 'countryId', CountryDefinition::class),
            new FkField('customer_group_id', 'customerGroupId', CustomerGroupDefinition::class),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new FkField('moorl_merchant_marker_id', 'markerId', MarkerDefinition::class),
            new FkField('cms_page_id', 'cmsPageId', CmsPageDefinition::class),

            (new StringField('first_name', 'firstName'))->addFlags(new EditField('text')),
            (new StringField('last_name', 'lastName'))->addFlags(new EditField('text')),
            (new StringField('title', 'title'))->addFlags(new EditField('text')),
            (new StringField('street', 'street'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new EditField('text')),
            (new StringField('street_number', 'streetNumber'))->addFlags(new EditField('text')),
            (new StringField('email', 'email'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new EditField('text')),
            (new StringField('company', 'company'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING),new EditField('text')),
            (new StringField('zipcode', 'zipcode'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new EditField('text')),
            (new StringField('city', 'city'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new EditField('text')),
            (new StringField('country_code', 'countryCode'))->addFlags(new EditField('text')),
            (new FloatField('location_lat','locationLat'))->addFlags(new EditField('number')),
            (new FloatField('location_lon','locationLon'))->addFlags(new EditField('number')),
            (new StringField('department', 'department'))->addFlags(new EditField('text')),
            (new StringField('vat_id', 'vatId'))->addFlags(new EditField('text')),
            (new StringField('phone_number', 'phoneNumber'))->addFlags(new EditField('text')),
            (new StringField('description', 'description')),
            (new StringField('opening_hours', 'openingHours')),
            (new StringField('additional_address_line1', 'additionalAddressLine1'))->addFlags(new EditField('text')),
            (new StringField('additional_address_line2', 'additionalAddressLine2'))->addFlags(new EditField('text')),
            (new StringField('shop_url', 'shopUrl'))->addFlags(new EditField('text')),
            (new StringField('merchant_url', 'merchantUrl'))->addFlags(new EditField('text')),

            (new IntField('auto_increment', 'autoIncrement'))->addFlags(new WriteProtected()),

            new CustomFields(),
            new JsonField('data', 'data'),

            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true))->addFlags(new EditField(), new LabelProperty('fileName')),
            (new ManyToOneAssociationField('marker', 'moorl_merchant_marker_id', MarkerDefinition::class, 'id', true)),
            (new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToOneAssociationField('customerGroup', 'customer_group_id', CustomerGroupDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToOneAssociationField('cmsPage', 'cms_page_id', CmsPageDefinition::class, 'id', false))->addFlags(new EditField(), new LabelProperty('name')),

            (new ManyToManyAssociationField('categories', CategoryDefinition::class, MerchantCategoryDefinition::class, 'moorl_merchant_id', 'category_id'))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('tags', TagDefinition::class, MerchantTagDefinition::class, 'moorl_merchant_id', 'tag_id'))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('productManufacturers', ProductManufacturerDefinition::class, MerchantProductManufacturerDefinition::class, 'moorl_merchant_id', 'product_manufacturer_id'))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToManyAssociationField('products', ProductDefinition::class, MerchantStockDefinition::class, 'moorl_merchant_id', 'product_id'))->addFlags(new EditField(), new LabelProperty('productNumber')),

            new OneToManyAssociationField('merchantOpeningHours', OpeningHourDefinition::class, 'moorl_merchant_id'),
            new OneToManyAssociationField('merchantStocks', MerchantStockDefinition::class, 'moorl_merchant_id'),
            new OneToManyAssociationField('customers', MerchantCustomerDefinition::class, 'moorl_merchant_id'),

            new TranslationsAssociationField(MerchantTranslationDefinition::class, 'moorl_merchant_id'),

            (new DistanceField('distance', 'location_lat', 'location_lon'))->addFlags(new Runtime()),
        ]);
    }
}
