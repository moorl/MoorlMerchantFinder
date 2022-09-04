<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Page\Merchant;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Storefront\Page\Page;

class MerchantPage extends Page
{
    protected MerchantEntity $merchant;
    protected ProductListingResult $products;
    protected ?CmsPageEntity $cmsPage = null;

    /**
     * @return CmsPageEntity|null
     */
    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    /**
     * @param CmsPageEntity|null $cmsPage
     */
    public function setCmsPage(?CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    /**
     * @return MerchantEntity
     */
    public function getMerchant(): MerchantEntity
    {
        return $this->merchant;
    }

    /**
     * @param MerchantEntity $merchant
     */
    public function setMerchant(MerchantEntity $merchant): void
    {
        $this->merchant = $merchant;
    }

    /**
     * @return ProductListingResult
     */
    public function getProducts(): ProductListingResult
    {
        return $this->products;
    }

    /**
     * @param ProductListingResult $products
     */
    public function setProducts(ProductListingResult $products): void
    {
        $this->products = $products;
    }

    public function getEntityName(): string
    {
        return MerchantDefinition::ENTITY_NAME;
    }
}
