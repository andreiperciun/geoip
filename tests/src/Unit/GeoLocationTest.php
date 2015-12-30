<?php

/**
 * @file
 * Contains \Drupal\Tests\geoip\Unit\GeoLocationTest.
 */

namespace Drupal\Tests\geoip\Unit;

use Drupal\geoip\GeoLocation;
use Drupal\Tests\UnitTestCase;
use Drupal\geoip\GeoLocatorManager;
use CommerceGuys\Intl\Country\CountryRepositoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\geoip\Plugin\GeoLocator\GeoLocatorInterface;
use CommerceGuys\Intl\Country\CountryInterface;

/**
 * Tests the geolocator plugin manager.
 *
 * @coversDefaultClass \Drupal\geoip\GeoLocation
 *
 * @group GeoIP
 */
class GeoLocationTest extends UnitTestCase {

  /**
   * Test getGeoLocatorId.
   *
   * @covers ::getGeoLocatorId
   */
  public function testGetGeoLocatorId() {
    $geolocators_manager = $this->prophesize(GeoLocatorManager::class);
    $country_repository = $this->prophesize(CountryRepositoryInterface::class);
    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $cache_backend = $this->prophesize(CacheBackendInterface::class);

    $config_factory->get('geoip.geolocation')->willReturn([
      'plugin_id' => 'local',
      'debug' => FALSE,
    ]);

    $geolocation = new GeoLocation($geolocators_manager->reveal(), $country_repository->reveal(), $config_factory->reveal(), $cache_backend->reveal());

    $this->assertEquals('local', $geolocation->getGeoLocatorId());
  }

  /**
   * Test getGeoLocator.
   *
   * @covers ::getGeoLocator
   */
  public function testGetGeoLocator() {
    $geolocators_manager = $this->prophesize(GeoLocatorManager::class);
    $country_repository = $this->prophesize(CountryRepositoryInterface::class);
    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $cache_backend = $this->prophesize(CacheBackendInterface::class);

    $geolocators_manager->createInstance('local')->willReturn($this->prophesize(GeoLocatorInterface::class)->reveal());
    $config_factory->get('geoip.geolocation')->willReturn([
      'plugin_id' => 'local',
      'debug' => FALSE,
    ]);

    $geolocation = new GeoLocation($geolocators_manager->reveal(), $country_repository->reveal(), $config_factory->reveal(), $cache_backend->reveal());
    $locator = $geolocation->getGeoLocator();

    $this->assertTrue($locator instanceof GeoLocatorInterface);
  }

  /**
   * Test getGeoLocator.
   *
   * @covers ::geolocate
   */
  public function testGeolocate() {
    $geolocators_manager = $this->prophesize(GeoLocatorManager::class);
    $country_repository = $this->prophesize(CountryRepositoryInterface::class);
    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $cache_backend = $this->prophesize(CacheBackendInterface::class);

    $locator = $this->prophesize(GeoLocatorInterface::class);
    $locator->geolocate('127.0.0.1')->willReturn(NULL);
    $locator->geolocate('2605:a000:140d:c18f:5995:dfe1:7914:4b4f')->willReturn('US');
    $locator->geolocate('23.86.161.12')->willReturn('CA');

    $geolocators_manager->createInstance('local')->willReturn($locator->reveal());

    $country_repository->get('US')->willReturn($this->prophesize(CountryInterface::class)->reveal());
    $country_repository->get('CA')->willReturn($this->prophesize(CountryInterface::class)->reveal());

    $config_factory->get('geoip.geolocation')->willReturn([
      'plugin_id' => 'local',
      'debug' => FALSE,
    ]);

    $geolocation = new GeoLocation($geolocators_manager->reveal(), $country_repository->reveal(), $config_factory->reveal(), $cache_backend->reveal());

    $this->assertNull($geolocation->geolocate('127.0.0.1'));
    $this->assertTrue($geolocation->geolocate('2605:a000:140d:c18f:5995:dfe1:7914:4b4f') instanceof CountryInterface);
    $this->assertTrue($geolocation->geolocate('23.86.161.12') instanceof CountryInterface);
  }

}
