<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantCustomer;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MerchantCustomerEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $customerId;
    /**
     * @var string|null
     */
    protected $merchantId;
    /**
     * @var MerchantEntity|null
     */
    protected $merchant;
    /**
     * @var CustomerEntity|null
     */
    protected $customer;
    /**
     * @var string|null
     */
    protected $customerNumber;
    /**
     * @var string|null
     */
    protected $info;

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    public function setMerchantId(?string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function getMerchant(): ?MerchantEntity
    {
        return $this->merchant;
    }

    public function setMerchant(?MerchantEntity $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomerNumber(): ?string
    {
        return $this->customerNumber;
    }

    public function setCustomerNumber(?string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): void
    {
        $this->info = $info;
    }
}
