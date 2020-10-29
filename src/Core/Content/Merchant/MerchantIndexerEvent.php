<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\NestedEvent;

class MerchantIndexerEvent extends NestedEvent
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var array
     */
    private $ids;

    public function __construct(array $ids, Context $context)
    {
        $this->context = $context;
        $this->ids = $ids;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getIds(): array
    {
        return $this->ids;
    }
}
