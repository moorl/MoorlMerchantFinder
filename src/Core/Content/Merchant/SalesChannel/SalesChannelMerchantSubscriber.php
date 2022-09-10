<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use MoorlFoundation\Core\Content\Marker\MarkerCollection;
use MoorlFoundation\Core\Content\Marker\MarkerDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SalesChannelMerchantSubscriber implements EventSubscriberInterface
{
    private DefinitionInstanceRegistry $definitionInstanceRegistry;
    private SystemConfigService $systemConfigService;
    private ?MarkerCollection $markers = null;

    public function __construct(
        DefinitionInstanceRegistry $definitionInstanceRegistry,
        SystemConfigService $systemConfigService
    )
    {
        $this->definitionInstanceRegistry = $definitionInstanceRegistry;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sales_channel.moorl_merchant.loaded' => 'salesChannelLoaded'
        ];
    }

    public function loaded(EntityLoadedEvent $event): void
    {
        $this->initMarkers($event->getContext());

        $defaultMarker = $this->markers->get($this->systemConfigService->get('MoorlMerchantFinder.config.defaultMarker'));
        $highlightMarker = $this->markers->get($this->systemConfigService->get('MoorlMerchantFinder.config.highlightMarker'));

        /** @var MerchantEntity $entity */
        foreach ($event->getEntities() as $entity) {
            if ($entity->getMarkerId()) {
                continue;
            }

            if ($entity->getHighlight()) {
                $entity->setMarker($highlightMarker);
            } else {
                $entity->setMarker($defaultMarker);
            }
        }
    }

    public function salesChannelLoaded(SalesChannelEntityLoadedEvent $event): void
    {
        $this->initMarkers($event->getContext());

        $defaultMarker = $this->markers->get($this->systemConfigService->get(
            'MoorlMerchantFinder.config.defaultMarker',
            $event->getSalesChannelContext()->getSalesChannelId()
        ));
        $highlightMarker = $this->markers->get($this->systemConfigService->get(
            'MoorlMerchantFinder.config.highlightMarker',
            $event->getSalesChannelContext()->getSalesChannelId()
        ));

        /** @var SalesChannelMerchantEntity $entity */
        foreach ($event->getEntities() as $entity) {
            if ($entity->getMarkerId()) {
                continue;
            }

            if ($entity->getHighlight()) {
                $entity->setMarker($highlightMarker);
            } else {
                $entity->setMarker($defaultMarker);
            }
        }
    }

    private function initMarkers(Context $context): void
    {
        if ($this->markers) {
            return;
        }

        $criteria = new Criteria();
        $criteria->setLimit(100);
        $markerRepository = $this->definitionInstanceRegistry->getRepository(MarkerDefinition::ENTITY_NAME);

        $this->markers = $markerRepository->search($criteria, $context)->getEntities();
    }
}
