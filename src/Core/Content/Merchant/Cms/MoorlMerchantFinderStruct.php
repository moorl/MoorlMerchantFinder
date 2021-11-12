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
    protected EntityCollection $countries;
    protected EntityCollection $tags;
    protected EntityCollection $categories;
    protected EntityCollection $productManufacturers;
    protected EntityCollection $merchants;

    /**
     * @return EntityCollection
     */
    public function getCountries(): EntityCollection
    {
        return $this->countries;
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
     * @return EntityCollection
     */
    public function getTags(): EntityCollection
    {
        return $this->tags;
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
     * @return EntityCollection
     */
    public function getCategories(): EntityCollection
    {
        return $this->categories;
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
     * @return EntityCollection
     */
    public function getProductManufacturers(): EntityCollection
    {
        return $this->productManufacturers;
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
     * @return EntityCollection
     */
    public function getMerchants(): EntityCollection
    {
        return $this->merchants;
    }

    /**
     * @param EntityCollection $merchants
     */
    public function setMerchants(EntityCollection $merchants): void
    {
        $this->merchants = $merchants;
    }
}
