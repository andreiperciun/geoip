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
    'tokens' => array('user' => $tokens),
  );
}

/**
 * Implements hook_tokens().
 */
function geoip_tokens($type, $tokens, array $data = array(), array $options = array()) {
  $replacements = array();
  if ($type == 'user') {
    foreach($tokens as $token => $placeholder) {
      switch($token) {
        case 'geoip-country-code':
          $replacements[$token] = geoip_country_code();
          break;
        case 'geoip-country-name':
          $country_values = geoip_country_values();
          $country_code = geoip_country_code();
          $country_name = (isset($country_values[$country_code])) ? $country_values[$country_code] : t('Unknown');
          $replacements[$token] = $country_name;
          break;
      }
    }
  }
  return $replacements;
}
