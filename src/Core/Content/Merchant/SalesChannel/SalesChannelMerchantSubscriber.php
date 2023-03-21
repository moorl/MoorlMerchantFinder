<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use MoorlFoundation\Core\Content\Marker\MarkerCollection;
use MoorlFoundation\Core\Content\Marker\MarkerDefinition;
use Shopware\Core\Framework\Adapter\Twig\TemplateFinder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class SalesChannelMerchantSubscriber implements EventSubscriberInterface
{
    final public const POPUP_CONTENT_PATH = "@MoorlMerchantFinder/plugin/moorl-merchant-finder/component/merchant-listing/map-popup-content.html.twig";
    private ?MarkerCollection $markers = null;
    private ?string $popupContentTemplate = null;

    public function __construct(private readonly DefinitionInstanceRegistry $definitionInstanceRegistry, private readonly SystemConfigService $systemConfigService, private readonly TemplateFinder $templateFinder, private readonly Environment $twig)
    {
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

        $salesChannelContext = $event->getSalesChannelContext();

        $defaultMarker = $this->markers->get($this->systemConfigService->get(
            'MoorlMerchantFinder.config.defaultMarker',
            $salesChannelContext->getSalesChannelId()
        ));
        $highlightMarker = $this->markers->get($this->systemConfigService->get(
            'MoorlMerchantFinder.config.highlightMarker',
            $salesChannelContext->getSalesChannelId()
        ));

        /** @var SalesChannelMerchantEntity $entity */
        foreach ($event->getEntities() as $entity) {
            $entity->setPopupContent(
                $this->getPopupContent($entity, $salesChannelContext)
            );

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

    private function getPopupContent(SalesChannelMerchantEntity $merchant, SalesChannelContext $salesChannelContext): string
    {
        if (!$this->popupContentTemplate) {
            $this->popupContentTemplate = $this->templateFinder->find(self::POPUP_CONTENT_PATH);
        }

        if (isset($this->twig)) {
            return $this->twig->render($this->popupContentTemplate, [
                'merchant' => $merchant,
                'context' => $salesChannelContext
            ]);
        }

        return self::POPUP_CONTENT_PATH;
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
