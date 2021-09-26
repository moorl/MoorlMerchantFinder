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

    /**
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @param string $zipcode
     */
    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return float
     */
    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }

    /**
     * @param float $deliveryPrice
     */
    public function setDeliveryPrice(float $deliveryPrice): void
    {
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * @return float
     */
    public function getMinOrderValue(): float
    {
        return $this->minOrderValue;
    }

    /**
     * @param float $minOrderValue
     */
    public function setMinOrderValue(float $minOrderValue): void
    {
        $this->minOrderValue = $minOrderValue;
    }

    /**
     * @return int
     */
    public function getDeliveryTime(): int
    {
        return $this->deliveryTime;
    }

    /**
     * @param int $deliveryTime
     */
    public function setDeliveryTime(int $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }
}
