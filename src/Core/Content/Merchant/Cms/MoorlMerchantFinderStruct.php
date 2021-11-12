<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\Cms;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerCollection;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\System\Tag\TagCollection;

class MoorlMerchantFinderStruct extends Struct
{
    protected CountryCollection $countries;
    protected TagCollection $tags;
    protected CategoryCollection $categories;
    protected ProductManufacturerCollection $productManufacturers;

    public function getApiAlias(): string
    {
        return 'cms_magazine_article_listing';
    }
}
