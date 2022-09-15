<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Subscriber;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Content\Product\Events\ProductListingCollectFilterEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCriteriaSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingCollectFilterEvent::class => 'onProductListingCollectFilter'
        ];
    }

    public function onProductListingCollectFilter(ProductListingCollectFilterEvent $event): void
    {
        if (!$this->systemConfigService->get('MoorlMerchantFinder.config.productFilterMerchant')) {
            return;
        }

        $filters = $event->getFilters();
        $request = $event->getRequest();

        $ids = array_filter(explode('|', $request->query->get('merchant', '')));

        $filter = new Filter(
            'merchant',
            !empty($ids),
            [$this->getMerchantEntityAggregation()],
            new EqualsAnyFilter('product.MoorlMerchants.id', $ids),
            $ids
        );

        $filters->add($filter);
    }

    private function getMerchantEntityAggregation(): EntityAggregation
    {
        return new EntityAggregation('merchant', 'product.MoorlMerchants.id', MerchantDefinition::ENTITY_NAME);
    }
}
