<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Subscriber;

use Composer\IO\NullIO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Shopware\Core\Content\Cms\CmsPageEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StorefrontSubscriber implements EventSubscriberInterface
{
    private $systemConfigService;
    private $mediaRepository;
    private $connection;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $mediaRepository,
        Connection $connection
    )
    {
        $this->systemConfigService = $systemConfigService;
        $this->mediaRepository = $mediaRepository;
        $this->connection = $connection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageEvents::SLOT_LOADED_EVENT => 'onEntityLoadedEvent'
        ];
    }

    public function onEntityLoadedEvent(EntityLoadedEvent $event): void
    {
        return;
    }
}
