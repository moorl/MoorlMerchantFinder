<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Merchant;

use DateTimeImmutable;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class OpeningHourEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $merchantId;
    /**
     * @var MerchantEntity|null
     */
    protected $merchant;
    /**
     * @var DateTimeImmutable|null
     */
    protected $date;
    /**
     * @var DateTimeImmutable|null
     */
    protected $showFrom;
    /**
     * @var DateTimeImmutable|null
     */
    protected $showUntil;
    /**
     * @var bool|null
     */
    protected $repeat;
    /**
     * @var string|null
     */
    protected $title;
    /**
     * @var array|null
     */
    protected $openingHours;

    /**
     * @return string
     */
    public function getUniqueIdentifier(): string
    {
        return $this->_uniqueIdentifier;
    }

    /**
     * @param string $uniqueIdentifier
     */
    public function setUniqueIdentifier(string $uniqueIdentifier): void
    {
        $this->_uniqueIdentifier = $uniqueIdentifier;
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
     * @return DateTimeImmutable|null
     */
    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param DateTimeImmutable|null $date
     */
    public function setDate(?DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getShowFrom(): ?DateTimeImmutable
    {
        return $this->showFrom;
    }

    /**
     * @param DateTimeImmutable|null $showFrom
     */
    public function setShowFrom(?DateTimeImmutable $showFrom): void
    {
        $this->showFrom = $showFrom;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getShowUntil(): ?DateTimeImmutable
    {
        return $this->showUntil;
    }

    /**
     * @param DateTimeImmutable|null $showUntil
     */
    public function setShowUntil(?DateTimeImmutable $showUntil): void
    {
        $this->showUntil = $showUntil;
    }

    /**
     * @return bool|null
     */
    public function getRepeat(): ?bool
    {
        return $this->repeat;
    }

    /**
     * @param bool|null $repeat
     */
    public function setRepeat(?bool $repeat): void
    {
        $this->repeat = $repeat;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return array|null
     */
    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    /**
     * @param array|null $openingHours
     */
    public function setOpeningHours(?array $openingHours): void
    {
        $this->openingHours = $openingHours;
    }
}
