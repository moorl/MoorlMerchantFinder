<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Event;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantCollection;
use Shopware\Core\Framework\Context;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated: will be removed
 */
class MerchantsLoadedEvent extends Event
{
    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function getMerchants(): ?MerchantCollection
    {
        return $this->merchants;
    }

    public function setMerchants(?MerchantCollection $merchants): void
    {
        $this->merchants = $merchants;
    }

    public function __construct(private Context $context, private ?MerchantCollection $merchants)
    {
    }

    public function getName(): string
    {
        return 'moorl_merchant_finder.merchants_loaded';
    }
}
