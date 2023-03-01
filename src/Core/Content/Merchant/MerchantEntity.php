<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer\MerchantCustomerCollection;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockCollection;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockEntity;
use Moorl\MerchantFinder\MoorlMerchantFinder;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityAddressTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityCompanyTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityContactTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityCustomTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityLocationTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityOpeningHoursTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityPersonTrait;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityThingTrait;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\Tag\TagCollection;

class MerchantEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;
    use EntityAddressTrait;
    use EntityCompanyTrait;
    use EntityContactTrait;
    use EntityLocationTrait;
    use EntityOpeningHoursTrait;
    use EntityPersonTrait;
    use EntityThingTrait;
    use EntityCustomTrait;

    protected ?MerchantCustomerCollection $customers = null;
    protected ?MerchantStockCollection $merchantStocks = null;
    protected ?MerchantStockEntity $merchantStock = null;
    protected ?string $seoUrl = null;
    protected ?int $distance = null;
    protected bool $allowMerchantCustomerContact = false;
    protected bool $highlight = false;
    protected int $priority = 0;
    protected ?ProductManufacturerCollection $manufacturers = null;
    protected ?ProductManufacturerCollection $productManufacturers = null;
    protected ?TagCollection $tags = null;
    protected ?CategoryCollection $categories = null;
    protected ?string $salesChannelId = null;
    protected ?string $customerGroupId = null;
    protected ?ProductManufacturerEntity $manufacturer = null;
    protected ?SalesChannelEntity $salesChannel = null;
    protected ?array $data = null;
    protected ?string $department = null;
    protected ?CustomerEntity $customer = null;
    protected ?string $originId = null;
    protected ?string $countryCode = null;
    protected ?string $type = null;

    public function getCmsPageId(): ?string
    {
        return $this->cmsPageId ?: MoorlMerchantFinder::CMS_PAGE_MERCHANT_DEFAULT_ID;
    }

    /**
     * @return MerchantCustomerCollection|null
     */
    public function getCustomers(): ?MerchantCustomerCollection
    {
        return $this->customers;
    }

    /**
     * @param MerchantCustomerCollection|null $customers
     */
    public function setCustomers(?MerchantCustomerCollection $customers): void
    {
        $this->customers = $customers;
    }

    public function getMerchantStocks(): ?MerchantStockCollection
    {
        return $this->merchantStocks;
    }

    public function setMerchantStocks(?MerchantStockCollection $merchantStocks): void
    {
        $this->merchantStocks = $merchantStocks;
    }

    public function getMerchantStock(): ?MerchantStockEntity
    {
        return $this->merchantStock;
    }

    public function setMerchantStock(?MerchantStockEntity $merchantStock): void
    {
        $this->merchantStock = $merchantStock;
    }

    public function getSeoUrl(): ?string
    {
        return $this->seoUrl;
    }

    public function setSeoUrl(?string $seoUrl): void
    {
        $this->seoUrl = $seoUrl;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): void
    {
        $this->distance = $distance;
    }

    public function getAllowMerchantCustomerContact(): bool
    {
        return $this->allowMerchantCustomerContact;
    }

    public function setAllowMerchantCustomerContact(bool $allowMerchantCustomerContact): void
    {
        $this->allowMerchantCustomerContact = $allowMerchantCustomerContact;
    }

    public function getHighlight(): bool
    {
        return $this->highlight;
    }

    public function setHighlight(bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getManufacturers(): ?ProductManufacturerCollection
    {
        return $this->manufacturers;
    }

    public function setManufacturers(?ProductManufacturerCollection $manufacturers): void
    {
        $this->manufacturers = $manufacturers;
    }

    public function getProductManufacturers(): ?ProductManufacturerCollection
    {
        return $this->productManufacturers;
    }

    public function setProductManufacturers(?ProductManufacturerCollection $productManufacturers): void
    {
        $this->productManufacturers = $productManufacturers;
    }

    public function getTags(): ?TagCollection
    {
        return $this->tags;
    }

    public function setTags(?TagCollection $tags): void
    {
        $this->tags = $tags;
    }

    public function getCategories(): ?CategoryCollection
    {
        return $this->categories;
    }

    public function setCategories(?CategoryCollection $categories): void
    {
        $this->categories = $categories;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getCustomerGroupId(): ?string
    {
        return $this->customerGroupId;
    }

    public function setCustomerGroupId(?string $customerGroupId): void
    {
        $this->customerGroupId = $customerGroupId;
    }

    public function getManufacturer(): ?ProductManufacturerEntity
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?ProductManufacturerEntity $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }
    
    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): void
    {
        $this->department = $department;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getOriginId(): ?string
    {
        return $this->originId;
    }

    public function setOriginId(?string $originId): void
    {
        $this->originId = $originId;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }



    public function getAddressPlain(): ?string
    {
        if ($this->company) {
            return sprintf(
                "%s\n%s %s\n%s-%s %s",
                $this->company,
                $this->street,
                $this->streetNumber,
                $this->countryCode,
                $this->zipcode,
                $this->city
            );
        }

        return null;
    }

    public function getAddressHTML(): ?string
    {
        if ($this->company) {
            return sprintf('<p>%s</p>', nl2br($this->getAddressPlain()));
        }

        return null;
    }
}
