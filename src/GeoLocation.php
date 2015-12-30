<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 12/29/15
 * Time: 4:50 PM
 */

namespace Drupal\geoip;

use CommerceGuys\Intl\Country\CountryRepositoryInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\UseCacheBackendTrait;
use Drupal\Core\Config\ConfigFactoryInterface;

class GeoLocation {

  use UseCacheBackendTrait;

  /**
   * Plugin manager for GeoLocator plugins.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $geoLocatorManager;

  /**
   * Country repository.
   *
   * @var \CommerceGuys\Intl\Country\CountryRepositoryInterface
   */
  protected $countryRepository;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  protected $cacheKey = 'geolocated_ips';
  protected $cacheTags = ['geoip'];
  protected $locatedAddresses = [];
  protected $config = [];

  /**
   * Constructs a new GeoLocation object.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $geolocators_manager
   *   The geolocation locator plugin manager service to use.
   * @param \CommerceGuys\Intl\Country\CountryRepositoryInterface $country_repository
   *   The country repository service to use.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   */
  public function __construct(PluginManagerInterface $geolocators_manager, CountryRepositoryInterface $country_repository, ConfigFactoryInterface $config_factory, CacheBackendInterface $cache_backend) {
    $this->geoLocatorManager = $geolocators_manager;
    $this->countryRepository = $country_repository;
    $this->configFactory = $config_factory;
    $this->cacheBackend = $cache_backend;
    $this->config = $this->configFactory->get('geoip.geolocation');
  }

  /**
   * Gets the identifier of the default geolocator plugin.
   *
   * @return string
   *   Identifier of the default geolocator plugin.
   */
  public function getGeoLocatorId() {
    return $this->config['plugin_id'];
  }

  /**
   * Gets an instance of the default geolocator plugin.
   *
   * @return \Drupal\geoip\Plugin\GeoLocator\GeoLocatorInterface
   *   Instance of the default geolocator plugin.
   */
  public function getGeoLocator() {
    return $this->geoLocatorManager->createInstance($this->config['plugin_id']);
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
        $geolocator = $this->getGeoLocator();

        $result = $geolocator->geolocate($ip_address);
        if ($result) {
          $result = $this->countryRepository->get($result);
        }

        $this->locatedAddresses[$ip_address] = $result;
        $this->cacheBackend->set($this->cacheKey, $this->locatedAddresses, Cache::PERMANENT, $this->cacheTags);
      }
    }

    return $this->locatedAddresses[$ip_address];
  }
}
