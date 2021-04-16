<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Customer;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer\MerchantCustomerDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                'MoorlMerchants',
                MerchantDefinition::class,
                MerchantCustomerDefinition::class,
                'customer_id',
                'moorl_merchant_id'
            ))
        );
    }
}
