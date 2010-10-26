<?php
// $Id$

/**
 * @file
 * Helper functions to fetch basic values
 */

/**
 * Return a list of country codes.
 *
 * List consists of iso3166 codes and the maxmind special codes
 * defined here: http://www.maxmind.com/app/iso3166
 *
 * @return array
 */
function _geoip_country_values() {
  include_once DRUPAL_ROOT . '/includes/locale.inc';
  $countries = country_get_list();

  // MaxMind specific shortcuts
  $countries += array(
    'A1' => 'Anonymous Proxy',
    'A2' => 'Satellite Provider',
    'AP' => 'Asia/Pacific Region',
    'EU' => 'Europe',
    'O1' => 'Other Country',
  );
  return $countries;
}