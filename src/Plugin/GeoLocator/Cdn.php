<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 12/29/15
 * Time: 4:19 PM
 */

namespace Drupal\geoip\Plugin\GeoLocator;

/**
 * CDN geolocation provider.
 *
 * @GeoLocator(
 *   id = "cdn",
 *   label = "CDN",
 *   description = "Checks for geolocation headers sent by CDN services",
 *   weight = -10
 * )
 */
class Cdn extends GeoLocatorBase {

  /**
   * {@inheritdoc}
   */
  public function geolocate($ip_address) {

    if ($this->checkCloudflare()) {
      $country_code = $this->checkCloudflare();
    }
    elseif ($this->checkCloudFront()) {
      $country_code = $this->checkCloudFront();
    }
    elseif ($this->checkCustomHeader()) {
      $country_code = $this->checkCustomHeader();
    }
    else {
      // Could no geolocate based off of CDN.
      return NULL;
    }

    return $country_code;
  }

  /**
   * Check for Cloudflare geolocation header.
   *
   * @return string
   *   The country code specified in the header.
   */
  protected function checkCloudflare() {
    if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
      return $_SERVER['HTTP_CF_IPCOUNTRY'];
    }
  }

  /**
   * Check for Amazon CloudFront geolocation header.
   *
   * @return string
   *   The country code specified in the header.
   */
  protected function checkCloudFront() {
    if (!empty($_SERVER['HTTP_CLOUDFRONT_VIEWER_COUNTRY'])) {
      return $_SERVER['HTTP_CLOUDFRONT_VIEWER_COUNTRY'];
    }
  }

  /**
   * Check for a custom geolocation header.
   *
   * @return string
   *   The country code specified in the header.
   */
  protected function checkCustomHeader() {
    // @todo: Implement setting for custom header to check.
    return NULL;
  }

}
