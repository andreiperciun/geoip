<?php

/**
 * @file
 * Contains \Drupal\geoip\GeoLocation.
 */

namespace Drupal\geoip;

use CommerceGuys\Intl\Country\CountryRepositoryInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\UseCacheBackendTrait;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Service to interact with the default geolocator plugin for geolocation.
 */
class GeoLocation {

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
   */
  public function __construct(PluginManagerInterface $geolocators_manager, CountryRepositoryInterface $country_repository, ConfigFactoryInterface $config_factory) {
    $this->geoLocatorManager = $geolocators_manager;
    $this->countryRepository = $country_repository;
    $this->configFactory = $config_factory;
    $this->config = $this->configFactory->get('geoip.geolocation');
  }

  /**
   * Gets the identifier of the default geolocator plugin.
   *
   * @return string
   *   Identifier of the default geolocator plugin.
   */
  public function getGeoLocatorId() {
    return $this->config->get('plugin_id');
  }

  /**
   * Gets an instance of the default geolocator plugin.
   *
   * @return \Drupal\geoip\Plugin\GeoLocator\GeoLocatorInterface
   *   Instance of the default geolocator plugin.
   */
  public function getGeoLocator() {
    return $this->geoLocatorManager->createInstance($this->config->get('plugin_id'));
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
      $geolocator = $this->getGeoLocator();

      $result = $geolocator->geolocate($ip_address);
      if ($result) {
        $result = $this->countryRepository->get($result);
      }

      $this->locatedAddresses[$ip_address] = $result;
    }

    return $this->locatedAddresses[$ip_address];
  }
}
