<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Struct\Struct;

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
