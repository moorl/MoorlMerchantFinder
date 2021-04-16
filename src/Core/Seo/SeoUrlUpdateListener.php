<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Seo;

use Doctrine\DBAL\Connection;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantIndexerEvent;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SeoUrlUpdateListener implements EventSubscriberInterface
{
    /**
     * @var SeoUrlUpdater
     */
    private $seoUrlUpdater;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityIndexerRegistry
     */
    private $indexerRegistry;

    public function __construct(SeoUrlUpdater $seoUrlUpdater, Connection $connection, EntityIndexerRegistry $indexerRegistry)
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
        $this->connection = $connection;
        $this->indexerRegistry = $indexerRegistry;
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