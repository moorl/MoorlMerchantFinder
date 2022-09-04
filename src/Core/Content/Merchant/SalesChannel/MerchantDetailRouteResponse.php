<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class MerchantDetailRouteResponse extends StoreApiResponse
{
    protected $object;

    public function __construct(MerchantEntity $merchant)
    {
        parent::__construct(new ArrayStruct([
            'moorl_merchant' => $merchant,
        ], 'moorl_merchant_detail'));
    }

    public function getResult(): ArrayStruct
    {
        return $this->object;
    }

    public function getMerchant(): MerchantEntity
    {
        return $this->object->get('moorl_merchant');
    }
}
