<?php declare(strict_types=1);

namespace MoorlCreator\Storefront\Subscriber;

use MoorlCreator\Core\Content\Creator\CreatorDefinition;
use Shopware\Core\Content\Product\Events\ProductListingCollectFilterEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SalesChannel\Event\SalesChannelProcessCriteriaEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCriteriaSubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemConfigService;

    /**
     * ProductCriteriaSubscriber constructor.
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingCollectFilterEvent::class => 'onProductListingCollectFilter',
            'sales_channel.product.process.criteria' => 'processCriteria',
        ];
    }

    public function onProductListingCollectFilter(ProductListingCollectFilterEvent $event): void
    {
        if (!$this->systemConfigService->get('MoorlCreator.config.enableListingFilter')) {
            return;
        }

        $filters = $event->getFilters();
        $request = $event->getRequest();

        $ids = array_filter(explode('|', $request->query->get('creator', '')));

        $filter = new Filter(
            'creator',
            !empty($ids),
            [$this->getCreatorEntityAggregation()],
            new EqualsAnyFilter('product.creators.id', $ids),
            $ids
        );

        $filters->add($filter);
    }

    private function getCreatorEntityAggregation(): EntityAggregation
    {
        return new EntityAggregation('creator', 'product.creators.id', CreatorDefinition::ENTITY_NAME);
    }

    public function processCriteria(SalesChannelProcessCriteriaEvent $event): void
    {
        $criteria = $event->getCriteria();
        $criteria->addAssociation('creators.avatar');
    }
}
