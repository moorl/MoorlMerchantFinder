<?php

namespace Moorl\MerchantFinder\GeoLocation\Interfaces;

interface Polygon
{
    public function __construct($array);

    public function surroundsGeoPoint();
}
