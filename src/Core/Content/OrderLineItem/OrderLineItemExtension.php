<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\OrderLineItem;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockDefinition;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OrderLineItemExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return OrderLineItemDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToOneAssociationField(
                'MoorlMerchantStock',
                'MoorlMerchantStock',
                MerchantStockDefinition::class,
            ))
        );
    }
}
