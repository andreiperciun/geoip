<?php

/**
 * @file
 * Token callbacks for the geoip module.
 */

/**
 * Implements hook_token_info().
 */
function geoip_token_info() {
  $tokens['geoip-country-code'] = array(
    'name' => t('Iso3166 country code'),
    'description' => t('The country code as detected by the users ip.'),
  );
  $tokens['geoip-country-name'] = array(
    'name' => t('Country name'),
    'description' => t('The country name as detected by the users ip.'),
  );

  return array(
    'tokens' => array('site' => $tokens),
  );
}

/**
 * Implements hook_tokens().
 */
function geoip_tokens($type, $tokens, array $data = array(), array $options = array()) {
  $replacements = array();
  if ($type == 'site') {
    if (isset($tokens['geoip-country-code'])) {
      $replacements[$tokens['geoip-country-code']] = geoip_country_code();
    }

    if (isset($tokens['geoip-country-name'])) {
      $country_values = geoip_country_values();
      $country_code = geoip_country_code();
      $country_name = (isset($country_values[$country_code])) ? $country_values[$country_code] : t('Unknown');
      $replacements[$tokens['geoip-country-name']] = $country_name;
    }
  }
  return $replacements;
}
