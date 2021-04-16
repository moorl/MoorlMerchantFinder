<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Seo;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantCollection;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Content\Sitemap\Provider\UrlProviderInterface;
use Shopware\Core\Content\Sitemap\Service\ConfigHandler;
use Shopware\Core\Content\Sitemap\Struct\Url;
use Shopware\Core\Content\Sitemap\Struct\UrlResult;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class MerchantUrlProvider implements UrlProviderInterface
{
    public const CHANGE_FREQ = 'weekly';

    /**
     * @var EntityRepositoryInterface
     */
    private $repository;

    /**
     * @var ConfigHandler
     */
    private $configHandler;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private $seoUrlPlaceholderHandler;

    public function __construct(
        EntityRepositoryInterface $repository,
        ConfigHandler $configHandler,
        SeoUrlPlaceholderHandlerInterface $seoUrlPlaceholderHandler
    ) {
        $this->repository = $repository;
        $this->configHandler = $configHandler;
        $this->seoUrlPlaceholderHandler = $seoUrlPlaceholderHandler;
    }

    public function getName(): string
    {
        return 'moorl_merchant_page';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls(SalesChannelContext $salesChannelContext, int $limit, ?int $offset = null): UrlResult
    {
        $collection = $this->getCollection($salesChannelContext, $limit, $offset);

        $urls = [];
        $url = new Url();
        foreach ($collection as $entity) {
            /** @var \DateTimeInterface $lastmod */
            $lastmod = $entity->getUpdatedAt() ?: $entity->getCreatedAt();

            $newUrl = clone $url;
            $newUrl->setLoc($this->seoUrlPlaceholderHandler->generate('moorl.merchant-finder.merchant.page', [
                'merchantId' => $entity->getId(),
            ]));
            $newUrl->setLastmod($lastmod);
            $newUrl->setChangefreq(self::CHANGE_FREQ);
            $newUrl->setResource(MerchantEntity::class);
            $newUrl->setIdentifier($entity->getId());

            $urls[] = $newUrl;
        }

        if (\count($urls) < $limit) { // last run
            $nextOffset = null;
        } elseif ($offset === null) { // first run
            $nextOffset = $limit;
        } else { // 1+n run
            $nextOffset = $offset + $limit;
        }

        return new UrlResult($urls, $nextOffset);
    }

    private function getCollection(SalesChannelContext $salesChannelContext, int $limit, ?int $offset, Criteria $collectionCriteria = null): MerchantCollection
    {
        if (!$collectionCriteria) {
            $collectionCriteria = new Criteria();
        }
        $collectionCriteria->setLimit($limit);

        if ($offset !== null) {
            $collectionCriteria->setOffset($offset);
        }

        /** @var MerchantCollection $collection */
        $collection = $this->repository->search($collectionCriteria, $salesChannelContext->getContext())->getEntities();

        return $collection;
    }
}
