<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantStock;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;

class MerchantStockEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $productId;
    /**
     * @var string|null
     */
    protected $merchantId;
    /**
     * @var string|null
     */
    protected $deliveryTimeId;
    /**
     * @var MerchantEntity|null
     */
    protected $merchant;
    /**
     * @var ProductEntity|null
     */
    protected $product;
    /**
     * @var DeliveryTimeEntity|null
     */
    protected $deliveryTime;
    /**
     * @var bool|null
     */
    protected $isStock;
    /**
     * @var int|null
     */
    protected $stock;

    /**
     * @return string|null
     */
    public function getProductId(): ?string
    {
        return $this->productId;
    }

    /**
     * @param string|null $productId
     */
    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    /**
     * @param string|null $merchantId
     */
    public function setMerchantId(?string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

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
     * @return ProductEntity|null
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * @param ProductEntity|null $product
     */
    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
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
     * @return bool|null
     */
    public function getIsStock(): ?bool
    {
        return $this->isStock;
    }

    /**
     * @param bool|null $isStock
     */
    public function setIsStock(?bool $isStock): void
    {
        $this->isStock = $isStock;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $stock
     */
    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }
}
