<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\System\Tag\TagCollection;
use Shopware\Core\System\Tag\TagEntity;

class MoorlMerchantFinderStruct extends Struct
{
    protected ?EntityCollection $countries = null;
    protected ?EntityCollection $tags = null;
    protected ?EntityCollection $categories = null;
    protected ?EntityCollection $productManufacturers = null;
    protected ?EntityCollection $merchants = null;

    public function getCountries(): ?EntityCollection
    {
        return $this->countries;
    }

    public function getTags(): ?EntityCollection
    {
        return $this->tags;
    }

    public function getCategories(): ?EntityCollection
    {
        return $this->categories;
    }

    public function getProductManufacturers(): ?EntityCollection
    {
        return $this->productManufacturers;
    }

    public function getMerchants(): ?EntityCollection
    {
        return $this->merchants;
    }

    public function setCountries(EntityCollection $countries): void
    {
        if ($countries instanceof CountryCollection) {
            $countries->sortByPositionAndName();
        }

        $this->countries = $countries;
    }

    public function setTags(EntityCollection $tags): void
    {
        if ($tags instanceof TagCollection) {
            $tags->sort(fn(TagEntity $a, TagEntity $b) => strnatcasecmp((string) $a->getName(), (string) $b->getName()));
        }

        $this->tags = $tags;
    }

    public function setCategories(EntityCollection $categories): void
    {
        if ($categories instanceof CategoryCollection) {
            $categories->sortByName();
        }

        $this->categories = $categories;
    }

    public function setProductManufacturers(EntityCollection $productManufacturers): void
    {
        if ($productManufacturers instanceof ProductManufacturerCollection) {
            $productManufacturers->sort(function (ProductManufacturerEntity $a, ProductManufacturerEntity $b) {
                if ($a->getName() && $b->getName()) {
                    return strnatcasecmp((string) $a->getName(), (string) $b->getName());
                }
                return strnatcasecmp((string) $a->getTranslation('name'), (string) $b->getTranslation('name'));
            });
        }

        $this->productManufacturers = $productManufacturers;
    }

    public function setMerchants(EntityCollection $merchants): void
    {
        $this->merchants = $merchants;
    }
}
