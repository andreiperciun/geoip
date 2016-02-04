<?php

/**
 * @file
 * Contains \Drupal\geoip\Plugin\GeoLocator\WebService.
 */

namespace Drupal\geoip\Plugin\GeoLocator;

use GeoIp2\WebService;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\WebService\Client;
use GeoIp2\Exception\OutOfQueriesException;

/**
 * Maxmind GeoIP2 Precision Web Service geolocation provider.
 *
 * @GeoLocator(
 *   id = "geoip2webservice",
 *   label = "GeoIP2 WebService",
 *   description = "Uses Maxmind GeoIP2 Precision Web Service",
 *   weight = -15
 * )
 */
class GeoIp2WebService extends GeoLocatorBase {

  const LICENSE_TYPE_COUNTRY    = 'country';
  const LICENSE_TYPE_CITY       = 'city';
  const LICENSE_TYPE_INSIGHTS   = 'insights';
  const CACHE_PREFIX = 'geoip';

  /** @var \Drupal\Core\Config\ImmutableConfig $conf */
  private $conf;

  /** @var string $licenseType */
  private $licenseType;

  public function __construct() {
    $this->conf = \Drupal::config('geoip.geolocation');
    $this->licenseType = $this->conf->get('license_type');
  }

  /**
   * {@inheritdoc}
   */
  public function geolocate($ip_address) {
    if ($record = $this->getCache($ip_address)) {
      return $record;
    }

    /** @var \GeoIp2\WebService\Client $client */
    $client = new Client($this->conf->get('user_id'), $this->conf->get('license_key'), array('en'));

    if (!$client) {
      return NULL;
    }

    try {
      switch($this->licenseType) {
        case self::LICENSE_TYPE_CITY:
          $record = $client->city($ip_address);
          break;
        case self::LICENSE_TYPE_INSIGHTS:
          $record = $client->insights($ip_address);
          break;
        default:
          $record = $client->country($ip_address);
          break;
      }

      $record = $this->processGeoIp2Model($record);

      // Cache request.
      $this->setCache($ip_address, $record);

      return $record;
    }
    catch (AddressNotFoundException $e) {
      return NULL;
    }
    catch (OutOfQueriesException $e) {
      \Drupal::logger('geoip')->error($this->t($e->getMessage()));
      return NULL;
    }
  }

  /**
   * Get country info.
   *
   * @param string $ip_address
   *   IP address.
   *
   * @return object
   *   Country info.
   */
  public function getCountryInfo($ip_address) {
    return $this->geolocate($ip_address)['country'];
  }

  /**
   * Get city info.
   *
   * @param string $ip_address
   *   IP address.
   *
   * @return object
   *   City info.
   */
  public function getCityInfo($ip_address) {
    return in_array($this->licenseType, array(self::LICENSE_TYPE_CITY, self::LICENSE_TYPE_INSIGHTS))
      ? $this->geolocate($ip_address)['city'] : NULL;
  }

  /**
   * Get result from cache.
   *
   * @param string $ip_address
   *
   * @return object
   *   GeoIp2 record.
   */
  private function getCache($ip_address) {
    $cache = \Drupal::cache()->get($this->cacheKey($ip_address));
    return is_object($cache) ? $cache->data : NULL;
  }

  /**
   * Save result in cache.
   *
   * @param string $ip_adress
   *   IP address.
   * @param object $data
   *   GeoIp2 model.
   */
  private function setCache($ip_address, $data) {
    \Drupal::cache()->set($this->cacheKey($ip_address), $data);
  }

  /**
   * Get cache key.
   *
   * @param string $ip_address
   *   IP address
   *
   * @return string
   *   Cache key.
   */
  private function cacheKey($ip_address) {
    return self::CACHE_PREFIX . ':' . $this->licenseType . ':' . $ip_address;
  }

  /**
   * Process model and return an array.
   *
   * @param object $model
   *
   * @return array
   *   Array of data.
   */
  private function processGeoIp2Model($model) {
    return $model->jsonSerialize();
  }
}
