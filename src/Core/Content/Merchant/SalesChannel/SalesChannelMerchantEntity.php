<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Core\Content\Merchant\SalesChannel;

use Moorl\MerchantFinder\Core\Content\Merchant\MerchantEntity;

class SalesChannelMerchantEntity extends MerchantEntity
{
    protected string $popupContent = "plugin/moorl-merchant-finder/component/merchant-listing/map-popup-content.html.twig";

    public function setPopupContent(string $popupContent): void
    {
        $this->popupContent = $popupContent;
    }

    public function getLeafletLocation(): array
    {
        return [
            'entityId' => $this->id,
            'latlng' => [
                $this->locationLat,
                $this->locationLon,
            ],
            'icon' => $this->marker ? $this->marker->getLeafletMarker() : false,
            'popup' => $this->popupContent
        ];
    }
}
