<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantArea;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MerchantAreaEntity extends Entity
{
    use EntityIdTrait;

    protected string $zipcode;
    protected float $deliveryPrice;
    protected float $minOrderValue;
    protected int $deliveryTime;

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(float $deliveryPrice): void
    {
        $this->deliveryPrice = $deliveryPrice;
    }

    public function getMinOrderValue(): float
    {
        return $this->minOrderValue;
    }

    public function setMinOrderValue(float $minOrderValue): void
    {
        $this->minOrderValue = $minOrderValue;
    }

    public function getDeliveryTime(): int
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(int $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }
}
