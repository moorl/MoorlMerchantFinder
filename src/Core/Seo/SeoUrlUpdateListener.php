<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Seo;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantIndexerEvent;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SeoUrlUpdateListener implements EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;


    public function __construct(SeoUrlUpdater $seoUrlUpdater)
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
    }

    public static function getSubscribedEvents()
    {
        return [
            MerchantIndexerEvent::class => 'updateMerchantUrls'
        ];
    }

    public function updateMerchantUrls(MerchantIndexerEvent $event): void
    {
        $ids = $event->getIds();

        $this->seoUrlUpdater->update(MerchantSeoUrlRoute::ROUTE_NAME, $ids);
    }
}
