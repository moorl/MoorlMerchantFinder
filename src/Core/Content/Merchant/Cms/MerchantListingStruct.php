<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\Struct;

class MerchantListingStruct extends Struct
{
    /**
     * @var EntitySearchResult|null
     */
    protected $listing;

    public function getListing(): ?EntitySearchResult
    {
        return $this->listing;
    }

    public function setListing(EntitySearchResult $listing): void
    {
        $this->listing = $listing;
    }

    public function getApiAlias(): string
    {
        return 'cms_merchant_listing';
    }
}
