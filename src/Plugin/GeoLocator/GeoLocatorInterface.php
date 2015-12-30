<?php

/**
 * @file
 * Contains \Drupal\geoip\Plugin\GeoLocator\GeoLocatorInterface.
 */

namespace Drupal\geoip\Plugin\GeoLocator;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Interface GeoLocatorInterface.
 */
interface GeoLocatorInterface {

  /**
   * Get the plugin's ID.
   *
   * @return string
   *   The geolocator ID
   */
  public function getId();

  /**
   * Get the plugin's label.
   *
   * @return string
   *   The geolocator label
   */
  public function getLabel();

  /**
   * Get the plugin's description.
   *
   * @return string
   *   The geolocator description
   */
  public function getDescription();

  /**
   * Get the plugin's weight.
   *
   * @return int
   *   The weight.
   */
  public function getWeight();

  /**
   * Performs geolocation on an address.
   *
   * @param string $ip_address
   *   The IP address to geolocate.
   *
   * @return array
   *   Array of geolocation information.
   */
  public function geolocate($ip_address);
}
