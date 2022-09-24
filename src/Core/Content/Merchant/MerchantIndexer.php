<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use MoorlFoundation\Core\Framework\DataAbstractionLayer\Indexer\EntityLocation\EntityLocationIndexer;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;

class MerchantIndexer extends EntityLocationIndexer
{
    public function handle(EntityIndexingMessage $message): void
    {
        parent::handle($message);

        $ids = $message->getData();
        $context = Context::createDefaultContext();

        $this->eventDispatcher->dispatch(new MerchantIndexerEvent($ids, $context));
    }
}
