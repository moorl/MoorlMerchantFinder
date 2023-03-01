<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Seo;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlExtractIdResult;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class MerchantSeoUrlRoute implements SeoUrlRouteInterface
{
    final public const ROUTE_NAME = 'moorl.merchant.detail';
    final public const DEFAULT_TEMPLATE = 'merchant/{{ merchant.translated.name }}';

    public function __construct(private readonly MerchantDefinition $entityDefinition, private readonly EntityRepository $repository)
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

    public function extractIdsToUpdate(EntityWrittenContainerEvent $event): SeoUrlExtractIdResult
    {
        $ids = [];

        $entityEvent = $event->getEventByEntityName(MerchantDefinition::ENTITY_NAME);
        if ($entityEvent) {
            $ids = $entityEvent->getIds();
        }

        return new SeoUrlExtractIdResult($ids);
    }

    public function prepareCriteria(Criteria $criteria, SalesChannelEntity $salesChannel): void
    {
    }

    public function getSeoVariables(): array
    {
        return [];
    }
}
