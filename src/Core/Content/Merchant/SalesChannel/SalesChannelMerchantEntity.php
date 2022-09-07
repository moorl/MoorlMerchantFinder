<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;

class SalesChannelMerchantEntity extends MerchantEntity
{
    /**
     * @return array
     */
    public function getLeafletLocation(): array
    {
        return [
            'entityId' => $this->id,
            'latlng' => [
                $this->locationLat,
                $this->locationLon,
            ],
            'icon' => $this->marker ? $this->marker->getLeafletMarker() : false,
            'popup' => sprintf(
                "<p><strong>%s</strong>%s %s<br>%s %s<br>%s %s<br></p>",
                $this->getTranslation('name'),
                $this->street,
                $this->streetNumber,
                $this->zipcode,
                $this->city,
                $this->countryState ? $this->countryState->getTranslation('name') : "",
                $this->country ? $this->country->getTranslation('name') : ""
            )
        ];
    }
}
