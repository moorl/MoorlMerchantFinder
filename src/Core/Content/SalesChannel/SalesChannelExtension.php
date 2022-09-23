<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantSalesChannel\MerchantSalesChannelDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class SalesChannelExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return SalesChannelDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                'MoorlMerchants',
                MerchantDefinition::class,
                MerchantSalesChannelDefinition::class,
                'sales_channel_id',
                'moorl_merchant_id'
            ))
        );
    }
}
