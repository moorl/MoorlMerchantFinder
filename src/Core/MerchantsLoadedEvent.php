<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core;

use Moorl\MerchantFinder\Merchant\MerchantCollection;
use Shopware\Core\Framework\Context;
use Symfony\Contracts\EventDispatcher\Event;

class MerchantsLoadedEvent extends Event
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * @return MerchantCollection|null
     */
    public function getMerchants(): ?MerchantCollection
    {
        return $this->merchants;
    }

    /**
     * @param MerchantCollection|null $merchants
     */
    public function setMerchants(?MerchantCollection $merchants): void
    {
        $this->merchants = $merchants;
    }
    /**
     * @var MerchantCollection|null
     */
    private $merchants;

    public function __construct(
        Context $context,
        MerchantCollection $merchants
    )
    {
        $this->context = $context;
        $this->merchants = $merchants;
    }

    public function getName(): string
    {
        return 'moorl_merchant_finder.merchants_loaded';
    }
}
