<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MerchantIndexer extends EntityIndexer
{
    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityRepositoryInterface
     */
    private $repository;

    /**
     * @var CacheClearer
     */
    private $cacheClearer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Connection $connection,
        IteratorFactory $iteratorFactory,
        EntityRepositoryInterface $repository,
        CacheClearer $cacheClearer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->repository = $repository;
        $this->connection = $connection;
        $this->cacheClearer = $cacheClearer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getName(): string
    {
        return 'moorl_magazine_article.indexer';
    }

    public function iterate($offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new MerchantIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $articleEvent = $event->getEventByEntityName(MerchantDefinition::ENTITY_NAME);

        if (!$articleEvent) {
            return null;
        }

        $ids = $articleEvent->getIds();
        foreach ($articleEvent->getWriteResults() as $result) {
            if (!$result->getExistence()) {
                continue;
            }
        }

        if (empty($ids)) {
            return null;
        }

        return new MerchantIndexingMessage(array_values($ids), null, $event->getContext(), \count($ids) > 20);
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();

        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return;
        }

        $context = Context::createDefaultContext();

        $this->eventDispatcher->dispatch(new MerchantIndexerEvent($ids, $context));

        $this->cacheClearer->invalidateIds($ids, MerchantDefinition::ENTITY_NAME);
    }
}