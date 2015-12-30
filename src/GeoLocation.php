<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 12/29/15
 * Time: 4:50 PM
 */

namespace Drupal\geoip;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\UseCacheBackendTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GeoLocation {

  use UseCacheBackendTrait;

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * Plugin manager for GeoLocator plugins.
   *
   * @var \Drupal\geoip\GeoLocatorsManagerInterface
   */
  protected $geoLocatorsManager;

  protected $cacheKey = 'geolocated_ips';
  protected $cacheTags = ['geoip'];
  protected $locatedAddresses = [];

  /**
   * Constructs a new GuardFactory object.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\geoip\GeoLocatorsManagerInterface $geo_locators_manager
   *   The geolocation locator plugin manager service to use.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   */
  public function __construct(ContainerInterface $container, GeoLocatorsManagerInterface $geo_locators_manager, CacheBackendInterface $cache_backend) {
    $this->container = $container;
    $this->geoLocatorsManager = $geo_locators_manager;

    $this->cacheBackend = $cache_backend;
  }

  /**
   * Geolocate an IP address.
   *
   * @param string $ip_address
   *   The IP address to geo locate.
   *
   * @return \CommerceGuys\Intl\Country\CountryInterface|null
   *   The geolocation result, or NULL if not able to be geolocated.
   */
  public function geolocate($ip_address) {
    if (!isset($this->locatedAddresses[$ip_address])) {
      if ($cache = $this->cacheBackend->get($this->cacheKey . ':' . $ip_address)) {
        $this->locatedAddresses[$ip_address] = $cache->data;
      }
      else {
        $result = NULL;
        foreach ($this->geoLocatorsManager->getLocators() as $locator) {
          $result = $locator->geolocate($ip_address);

          // If we have a result, break the loop and preserve response.
          if ($result !== NULL) {
            // @todo add to service def.
            // @todo should we initiate at return & not cache country object?
            /** @var \Drupal\address\Repository\CountryRepository $country_repository */
            $country_repository = \Drupal::service('address.country_repository');
            $result = $country_repository->get($result);
            break;
          }
        }

        $this->locatedAddresses[$ip_address] = $result;
        $this->cacheBackend->set($this->cacheKey, $this->locatedAddresses, Cache::PERMANENT, $this->cacheTags);
      }
    }

    return $this->locatedAddresses[$ip_address];
  }
}
