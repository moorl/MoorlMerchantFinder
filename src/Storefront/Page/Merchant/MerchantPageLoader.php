<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Storefront\Page\Merchant;

use Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel\MerchantDetailRoute;
use Moorl\MerchantFinder\MoorlMerchantFinder;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Product\SalesChannel\Listing\AbstractProductListingRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\Request;

class MerchantPageLoader
{
    public function __construct(
        private readonly GenericPageLoaderInterface $genericLoader,
        private readonly MerchantDetailRoute $merchantDetailRoute,
        private readonly AbstractProductListingRoute $productListingRoute
    )
    {
    }

    /**
     * @throws MissingRequestParameterException
     * @throws PageNotFoundException
     */
    public function load(Request $request, SalesChannelContext $context): MerchantPage
    {
        $merchantId = $request->attributes->get('merchantId');
        if (!$merchantId) {
            throw new MissingRequestParameterException('merchantId', '/merchantId');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('MoorlMerchants.id', $merchantId));
        $result = $this->productListingRoute->load(MoorlMerchantFinder::NAME, $request, $context, $criteria);
        $products = $result->getResult();

        $criteria = new Criteria();
        $result = $this->merchantDetailRoute->load($merchantId, $request, $context, $criteria);
        $merchant = $result->getMerchant();

        if (!$merchant->getActive()) {
            throw new PageNotFoundException($merchant->getId());
        }

        $page = $this->genericLoader->load($request, $context);

        /** @var MerchantPage $page */
        $page = MerchantPage::createFrom($page);
        $page->setProducts($products);
        $page->setMerchant($merchant);
        $page->setCmsPage($merchant->getCmsPage());

        $this->loadMetaData($page);

        return $page;
    }

    private function loadMetaData(MerchantPage $page): void
    {
        $metaInformation = $page->getMetaInformation();
        if (!$metaInformation) {
            return;
        }

        $metaDescription = $page->getMerchant()->getTranslation('metaDescription')
            ?? $page->getMerchant()->getTranslation('teaser');
        $metaInformation->setMetaDescription((string) $metaDescription);

        $metaInformation->setMetaKeywords((string) $page->getMerchant()->getTranslation('keywords'));

        if ((string) $page->getMerchant()->getTranslation('metaTitle') !== '') {
            $metaInformation->setMetaTitle((string) $page->getMerchant()->getTranslation('metaTitle'));
            return;
        }

        $metaTitleParts = [$page->getMerchant()->getTranslation('name')];
        $metaInformation->setMetaTitle(implode(' | ', $metaTitleParts));
    }
}
