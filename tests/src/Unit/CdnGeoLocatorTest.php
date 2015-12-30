<?php

/**
 * @file
 * Contains \Drupal\Tests\geoip\Unit\CdnGeoLocatorTest.
 */

namespace Drupal\Tests\geoip\Unit;


use Drupal\geoip\Plugin\GeoLocator\Cdn;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the CDN locator.
 *
 * @coversDefaultClass \Drupal\geoip\Plugin\GeoLocator\Cdn
 *
 * @group geoip
 */
class CdnGeoLocatorTest extends UnitTestCase {

  /**
   * Test the geolocate method for Cdn plugin.
   *
   * @covers ::geolocate
   * @backupGlobals disabled
   */
  public function testGeolocate() {
    $locator = new Cdn([], 'cdn', [
      'label' => 'CDN',
      'description' => 'Checks for geolocation headers sent by CDN services',
      'weight' => 10,
    ]);

    $this->assertEquals(NULL, $locator->geolocate('127.0.0.1'));

    $_SERVER['HTTP_CF_IPCOUNTRY'] = 'US';
    $this->assertEquals('US', $locator->geolocate('127.0.0.1'));

    $_SERVER['HTTP_CLOUDFRONT_VIEWER_COUNTRY'] = 'CA';
    // We can't equal CA since we manually check Cloudflare first.
    $this->assertNotEquals('CA', $locator->geolocate('127.0.0.1'));
    unset($_SERVER['HTTP_CF_IPCOUNTRY']);
    $this->assertEquals('CA', $locator->geolocate('127.0.0.1'));

    unset($_SERVER['HTTP_CLOUDFRONT_VIEWER_COUNTRY']);
    $_SERVER['HTTP_MY_CUSTOM_HEADER'] = 'FR';
    // @todo this needs to be updated when custom header implemented.
    $this->assertEquals(NULL, $locator->geolocate('127.0.0.1'));
  }

}
