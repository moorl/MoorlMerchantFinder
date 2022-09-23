<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityStockTrait;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;

class MerchantStockEntity extends Entity
{
    use EntityIdTrait;
    use EntityStockTrait;

    protected string $merchantId;
    protected ?MerchantEntity $merchant;
    protected bool $isStock = false;
    protected ?string $deliveryTimeId = null;
    protected ?DeliveryTimeEntity $deliveryTime = null;

    /**
     * @return string|null
     */
    public function getDeliveryTimeId(): ?string
    {
        return $this->deliveryTimeId;
    }

    /**
     * @param string|null $deliveryTimeId
     */
    public function setDeliveryTimeId(?string $deliveryTimeId): void
    {
        $this->deliveryTimeId = $deliveryTimeId;
    }

    /**
     * @return DeliveryTimeEntity|null
     */
    public function getDeliveryTime(): ?DeliveryTimeEntity
    {
        return $this->deliveryTime;
    }

    /**
     * @param DeliveryTimeEntity|null $deliveryTime
     */
    public function setDeliveryTime(?DeliveryTimeEntity $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return MerchantEntity|null
     */
    public function getMerchant(): ?MerchantEntity
    {
        return $this->merchant;
    }

    /**
     * @param MerchantEntity|null $merchant
     */
    public function setMerchant(?MerchantEntity $merchant): void
    {
        $this->merchant = $merchant;
    }

    /**
     * @return bool
     */
    public function isStock(): bool
    {
        return $this->isStock;
    }

    /**
     * @param bool $isStock
     */
    public function setIsStock(bool $isStock): void
    {
        $this->isStock = $isStock;
    }
}
