<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;

class MerchantIndexerEvent extends NestedEvent
{
    public function __construct(private readonly array $ids, private readonly Context $context, private readonly array $skip = [])
    {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getSkip(): array
    {
        return $this->skip;
    }
}
