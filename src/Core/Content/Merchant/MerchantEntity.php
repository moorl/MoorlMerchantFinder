<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant;

use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer\MerchantCustomerCollection;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockCollection;
use Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock\MerchantStockEntity;
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

    /**
     * @return MerchantStockCollection|null
     */
    public function getMerchantStocks(): ?MerchantStockCollection
    {
        return $this->merchantStocks;
    }

    /**
     * @param MerchantStockCollection|null $merchantStocks
     */
    public function setMerchantStocks(?MerchantStockCollection $merchantStocks): void
    {
        $this->merchantStocks = $merchantStocks;
    }

    /**
     * @return MerchantStockEntity|null
     */
    public function getMerchantStock(): ?MerchantStockEntity
    {
        return $this->merchantStock;
    }

    /**
     * @param MerchantStockEntity|null $merchantStock
     */
    public function setMerchantStock(?MerchantStockEntity $merchantStock): void
    {
        $this->merchantStock = $merchantStock;
    }

    /**
     * @return string|null
     */
    public function getSeoUrl(): ?string
    {
        return $this->seoUrl;
    }

    /**
     * @param string|null $seoUrl
     */
    public function setSeoUrl(?string $seoUrl): void
    {
        $this->seoUrl = $seoUrl;
    }

    /**
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->distance;
    }

    /**
     * @param int|null $distance
     */
    public function setDistance(?int $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return bool
     */
    public function getAllowMerchantCustomerContact(): bool
    {
        return $this->allowMerchantCustomerContact;
    }

    /**
     * @param bool $allowMerchantCustomerContact
     */
    public function setAllowMerchantCustomerContact(bool $allowMerchantCustomerContact): void
    {
        $this->allowMerchantCustomerContact = $allowMerchantCustomerContact;
    }

    /**
     * @return bool
     */
    public function getHighlight(): bool
    {
        return $this->highlight;
    }

    /**
     * @param bool $highlight
     */
    public function setHighlight(bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return ProductManufacturerCollection|null
     */
    public function getManufacturers(): ?ProductManufacturerCollection
    {
        return $this->manufacturers;
    }

    /**
     * @param ProductManufacturerCollection|null $manufacturers
     */
    public function setManufacturers(?ProductManufacturerCollection $manufacturers): void
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * @return ProductManufacturerCollection|null
     */
    public function getProductManufacturers(): ?ProductManufacturerCollection
    {
        return $this->productManufacturers;
    }

    /**
     * @param ProductManufacturerCollection|null $productManufacturers
     */
    public function setProductManufacturers(?ProductManufacturerCollection $productManufacturers): void
    {
        $this->productManufacturers = $productManufacturers;
    }

    /**
     * @return TagCollection|null
     */
    public function getTags(): ?TagCollection
    {
        return $this->tags;
    }

    /**
     * @param TagCollection|null $tags
     */
    public function setTags(?TagCollection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return CategoryCollection|null
     */
    public function getCategories(): ?CategoryCollection
    {
        return $this->categories;
    }

    /**
     * @param CategoryCollection|null $categories
     */
    public function setCategories(?CategoryCollection $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return string|null
     */
    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    /**
     * @param string|null $salesChannelId
     */
    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    /**
     * @return string|null
     */
    public function getCustomerGroupId(): ?string
    {
        return $this->customerGroupId;
    }

    /**
     * @param string|null $customerGroupId
     */
    public function setCustomerGroupId(?string $customerGroupId): void
    {
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return ProductManufacturerEntity|null
     */
    public function getManufacturer(): ?ProductManufacturerEntity
    {
        return $this->manufacturer;
    }

    /**
     * @param ProductManufacturerEntity|null $manufacturer
     */
    public function setManufacturer(?ProductManufacturerEntity $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }
    
    /**
     * @return SalesChannelEntity|null
     */
    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    /**
     * @param SalesChannelEntity|null $salesChannel
     */
    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @param string|null $department
     */
    public function setDepartment(?string $department): void
    {
        $this->department = $department;
    }

    /**
     * @return CustomerEntity|null
     */
    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    /**
     * @param CustomerEntity|null $customer
     */
    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return string|null
     */
    public function getOriginId(): ?string
    {
        return $this->originId;
    }

    /**
     * @param string|null $originId
     */
    public function setOriginId(?string $originId): void
    {
        $this->originId = $originId;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
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
