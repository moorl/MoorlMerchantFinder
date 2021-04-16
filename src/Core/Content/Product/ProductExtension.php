<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Product;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'MoorlMerchantStocks',
                MerchantStockDefinition::class,
                'product_id'
            )
        );

        $collection->add(
            (new ManyToManyAssociationField(
                'MoorlMerchants',
                MerchantDefinition::class,
                MerchantStockDefinition::class,
                'product_id',
                'moorl_merchant_id'
            ))
        );
    }
}
