<?php

namespace Moorl\MerchantFinder\GeoLocation\Interfaces;

use Moorl\MerchantFinder\GeoLocation\GeoPoint;

interface Polygon
{
    public function __construct($array);

    public function surroundsGeoPoint();
}
