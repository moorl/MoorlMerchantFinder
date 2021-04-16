<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\EditField;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\Field\Flags\LabelProperty;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MerchantCustomerDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'moorl_merchant_customer';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),

            (new FkField('moorl_merchant_id', 'merchantId', MerchantDefinition::class))->addFlags(new Required()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new Required()),

            (new StringField('customer_number', 'customerNumber'))->addFlags(new EditField('text')),
            (new StringField('info', 'info'))->addFlags(new EditField('text')),

            (new ManyToOneAssociationField('merchant', 'moorl_merchant_id', MerchantDefinition::class))->addFlags(new EditField(), new LabelProperty('name')),
            (new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class))->addFlags(new EditField(), new LabelProperty('customerNumber')),
        ]);
    }
}
