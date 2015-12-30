<?php

namespace Drupal\geoip;

use Drupal\Component\Plugin\PluginManagerInterface;

interface GeoLocatorsManagerInterface extends PluginManagerInterface {

  /**
   * Gets geo locators sorted by weight.
   *
   * @return \Drupal\geoip\Plugin\GeoLocator\GeoLocatorInterface[]
   *   Array of geolocator plugins keyed by machine name.
   */
  public function getLocators();

}
