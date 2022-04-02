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

    /**
     * @return EntityCollection|null
     */
    public function getCountries(): ?EntityCollection
    {
        return $this->countries;
    }

    /**
     * @return EntityCollection|null
     */
    public function getTags(): ?EntityCollection
    {
        return $this->tags;
    }

    /**
     * @return EntityCollection|null
     */
    public function getCategories(): ?EntityCollection
    {
        return $this->categories;
    }

    /**
     * @return EntityCollection|null
     */
    public function getProductManufacturers(): ?EntityCollection
    {
        return $this->productManufacturers;
    }

    /**
     * @return EntityCollection|null
     */
    public function getMerchants(): ?EntityCollection
    {
        return $this->merchants;
    }

    /**
     * @param EntityCollection $countries
     */
    public function setCountries(EntityCollection $countries): void
    {
        if ($countries instanceof CountryCollection) {
            $countries->sortByPositionAndName();
        }

        $this->countries = $countries;
    }

    /**
     * @param EntityCollection $tags
     */
    public function setTags(EntityCollection $tags): void
    {
        if ($tags instanceof TagCollection) {
            $tags->sort(function (TagEntity $a, TagEntity $b) {
                return strnatcasecmp($a->getName(), $b->getName());
            });
        }

        $this->tags = $tags;
    }

    /**
     * @param EntityCollection $categories
     */
    public function setCategories(EntityCollection $categories): void
    {
        if ($categories instanceof CategoryCollection) {
            $categories->sortByName();
        }

        $this->categories = $categories;
    }

    /**
     * @param EntityCollection $productManufacturers
     */
    public function setProductManufacturers(EntityCollection $productManufacturers): void
    {
        if ($productManufacturers instanceof ProductManufacturerCollection) {
            $productManufacturers->sort(function (ProductManufacturerEntity $a, ProductManufacturerEntity $b) {
                return strnatcasecmp($a->getName(), $b->getName());
            });
        }

        $this->productManufacturers = $productManufacturers;
    }

    /**
     * @param EntityCollection $merchants
     */
    public function setMerchants(EntityCollection $merchants): void
    {
        $this->merchants = $merchants;
    }
}
