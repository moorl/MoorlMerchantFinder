<?php

namespace Moorl\MerchantFinder\GeoLocation\Interfaces;
use Moorl\MerchantFinder\GeoLocation\Polygon;
use Moorl\MerchantFinder\GeoLocation\GeoPoint;

interface GeoPointInterface {
  public function inPolygon(Polygon $polygon);
  public function distanceTo(GeoPoint $geopoint, $unitofmeasure);
}

