<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelDefinitionInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class SalesChannelMerchantDefinition extends MerchantDefinition implements SalesChannelDefinitionInterface
{
    public function getEntityClass(): string
    {
        return SalesChannelMerchantEntity::class;
    }

    public function processCriteria(Criteria $criteria, SalesChannelContext $context): void
    {
        $criteria->addAssociation('media');
        $criteria->addAssociation('country');
        $criteria->addAssociation('productManufacturers');
        $criteria->addAssociation('tags');
        //$criteria->addAssociation('locationCache');

        if (!$this->hasAvailableFilter($criteria)) {
            $criteria->addFilter(
                new MerchantAvailableFilter($context)
            );
        }
    }

    private function hasAvailableFilter(Criteria $criteria): bool
    {
        foreach ($criteria->getFilters() as $filter) {
            if ($filter instanceof MerchantAvailableFilter) {
                return true;
            }
        }

        return false;
    }
}
