<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\OpeningHour;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                       add(OpeningHourEntity $entity)
 * @method void                       set(string $key, OpeningHourEntity $entity)
 * @method OpeningHourEntity[]    getIterator()
 * @method OpeningHourEntity[]    getElements()
 * @method OpeningHourEntity|null get(string $key)
 * @method OpeningHourEntity|null first()
 * @method OpeningHourEntity|null last()
 */
class OpeningHourCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OpeningHourEntity::class;
    }

    public function getForToday(?string $merchantId = null): ?OpeningHourEntity
    {
        $time = new \DateTimeImmutable();

        /* @var $element OpeningHourEntity */
        /* Fixed Date & Fixed Merchant */
        foreach ($this->elements as $element) {
            if ($element->getDate() && $element->getDate()->format("m-d") == $time->format("m-d") && $element->getMerchantId() == $merchantId) {
                return $element;
            }
        }
        /* In Date Range & Fixed Merchant */
        foreach ($this->elements as $element) {
            if ($element->getShowFrom() <= $time && $element->getShowUntil() >= $time && $element->getMerchantId() == $merchantId) {
                return $element;
            }
        }
        /* Fixed Date */
        foreach ($this->elements as $element) {
            if ($element->getDate() && $element->getDate()->format("m-d") == $time->format("m-d")) {
                return $element;
            }
        }
        /* In Date Range */
        foreach ($this->elements as $element) {
            if ($element->getShowFrom() <= $time && $element->getShowUntil() >= $time) {
                return $element;
            }
        }
        /* Fixed Merchant */
        foreach ($this->elements as $element) {
            if ($element->getMerchantId() == $merchantId) {
                return $element;
            }
        }
        /* Default */
        foreach ($this->elements as $element) {
            if (!$element->getMerchantId()) {
                return $element;
            }
        }

        return null;
    }
}
