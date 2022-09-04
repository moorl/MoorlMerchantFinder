<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"store-api"})
 */
class MerchantFollowRoute
{
    private EntityRepositoryInterface $merchantFollowRepository;

    public function __construct(
        EntityRepositoryInterface $merchantFollowRepository
    ) {
        $this->merchantFollowRepository = $merchantFollowRepository;
    }

    public function toggle(Request $request, SalesChannelContext $context): SuccessResponse
    {
        $customer = $context->getCustomer();
        if ($customer) {
            $merchantId = $request->get('merchantId');

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('merchantId', $merchantId));
            $criteria->addFilter(new EqualsFilter('customerId', $customer->getId()));

            $result = $this->merchantFollowRepository->search($criteria, $context->getContext())->count();

            if ($result === 0) {
                $this->merchantFollowRepository->upsert([[
                    'merchantId' => $merchantId,
                    'customerId' => $customer->getId(),
                ]], $context->getContext());
            } else {
                $this->merchantFollowRepository->delete([[
                    'merchantId' => $merchantId,
                    'customerId' => $customer->getId(),
                ]], $context->getContext());
            }
        }

        return new SuccessResponse();
    }
}
