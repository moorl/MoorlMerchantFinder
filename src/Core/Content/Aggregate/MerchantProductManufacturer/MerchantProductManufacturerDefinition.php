<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantProductManufacturer;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class MerchantProductManufacturerDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'moorl_merchant_product_manufacturer';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function isVersionAware(): bool
    {
        return true;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('moorl_merchant_id', 'moorlMerchantId', MerchantDefinition::class))->addFlags(new PrimaryKey(), new Required()),

            (new FkField('product_manufacturer_id', 'productManufacturerId', ProductManufacturerDefinition::class))->addFlags(new PrimaryKey(), new Required()),

            new ManyToOneAssociationField('moorlMerchant', 'moorl_merchant_id', MerchantDefinition::class, 'id', false),
            new ManyToOneAssociationField('productManufacturer', 'product_manufacturer_id', ProductManufacturerDefinition::class, 'id', false),
        ]);
    }
}
