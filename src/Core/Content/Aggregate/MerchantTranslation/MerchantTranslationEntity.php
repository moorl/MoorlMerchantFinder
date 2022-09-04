<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Aggregate\MerchantTranslation;

use MoorlFoundation\Core\Framework\DataAbstractionLayer\EntityThingTranslationTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class MerchantTranslationEntity extends TranslationEntity
{
    use EntityThingTranslationTrait;

    protected ?string $descriptionHtml = null;

    /**
     * @return string|null
     */
    public function getDescriptionHtml(): ?string
    {
        return $this->descriptionHtml;
    }

    /**
     * @param string|null $descriptionHtml
     */
    public function setDescriptionHtml(?string $descriptionHtml): void
    {
        $this->descriptionHtml = $descriptionHtml;
    }
}
