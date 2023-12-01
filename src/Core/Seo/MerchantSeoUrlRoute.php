<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Seo;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class MerchantSeoUrlRoute implements SeoUrlRouteInterface
{
    final public const ROUTE_NAME = 'frontend.moorl.merchant.detail';
    final public const DEFAULT_TEMPLATE = '{% if merchant.translated.seoUrl %}{{ merchant.translated.seoUrl }}{% else %}merchant/{{ merchant.translated.name }}{% endif %}';

    public function __construct(private readonly MerchantDefinition $entityDefinition)
    {
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->entityDefinition,
            self::ROUTE_NAME,
            self::DEFAULT_TEMPLATE,
            true
        );
    }

    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        if (!$entity instanceof MerchantEntity) {
            throw new \InvalidArgumentException('Expected MerchantEntity');
        }

        return new SeoUrlMapping(
            $entity,
            ['merchantId' => $entity->getId()],
            ['merchant' => $entity->jsonSerialize()]
        );
    }

    public function prepareCriteria(Criteria $criteria, SalesChannelEntity $salesChannel): void
    {
    }
}
