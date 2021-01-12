<?php

namespace Moorl\MerchantFinder\GeoLocation\Interfaces;

interface Polygon {
  public $coordinates = array();
  __construct(array $array) {}
  surroundsGeoPoint(GeoPoint) {}
}
