<?php

namespace Moorl\MerchantFinder\GeoLocation\Interfaces;
use Moorl\MerchantFinder\GeoLocation\GeoPoint;
use Moorl\MerchantFinder\GeoLocation\Polygon;

interface GeoPointInterface {
  public function inPolygon(Polygon $polygon);
  public function distanceTo(GeoPoint $geopoint, $unitofmeasure);
}

