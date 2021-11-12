<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class MerchantAvailableFilter extends MultiFilter
{
    public function __construct(SalesChannelContext $salesChannelContext, string $domain = 'moorl_merchant.')
    {
        $filters = [];

        $customer = $salesChannelContext->getCustomer();
        if ($customer) {
            $filters[] = new MultiFilter(
                MultiFilter::CONNECTION_OR, [
                    new EqualsFilter($domain . 'customerGroupId', null),
                    new EqualsFilter($domain . 'customerGroupId', $customer->getGroupId())
                ]
            );
            $filters[] = new MultiFilter(
                MultiFilter::CONNECTION_OR, [
                    new EqualsFilter($domain . 'customers.customerId', null),
                    new EqualsFilter($domain . 'customers.customerId', $customer->getId())
                ]
            );
        } else {
            $filters[] = new EqualsFilter($domain . 'customers.customerId', null);
            $filters[] = new EqualsFilter($domain . 'customerGroupId', null);
        }

        parent::__construct(
            self::CONNECTION_AND, array_merge(
                [
                    new EqualsFilter($domain . 'active', true),
                    new MultiFilter(
                        MultiFilter::CONNECTION_OR, [
                            new EqualsFilter($domain . 'salesChannels.id', null),
                            new EqualsFilter($domain . 'salesChannels.id', $salesChannelContext->getSalesChannelId())
                        ]
                    )
                ],
                $filters
            )
        );
    }
}
