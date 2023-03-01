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
    protected ?MerchantEntity $merchant = null;
    protected bool $isStock = false;
    protected ?string $deliveryTimeId = null;
    protected ?DeliveryTimeEntity $deliveryTime = null;

    public function getDeliveryTimeId(): ?string
    {
        return $this->deliveryTimeId;
    }

    public function setDeliveryTimeId(?string $deliveryTimeId): void
    {
        $this->deliveryTimeId = $deliveryTimeId;
    }

    public function getDeliveryTime(): ?DeliveryTimeEntity
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?DeliveryTimeEntity $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function setMerchantId(string $merchantId): void
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

    public function isStock(): bool
    {
        return $this->isStock;
    }

    public function setIsStock(bool $isStock): void
    {
        $this->isStock = $isStock;
    }
}
