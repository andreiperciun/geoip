<?php
// $Id: geoip_language.admin.inc,v 1.2 2010/05/19 16:15:41 drewish Exp $

/**
 * @file
 * Admin page callbacks for the GeoIP Language module.
 */

/**
 * Create the overview table of the already existing mappgins
 * 
 * @param array $languages
 * @return string
 *  HTML Table
 */
function theme_geoip_language_mapping_overview($languages) {

  $countries = geoip_country_values();
  $mapping = geoip_language_mappings(TRUE);

  $output = NULL;
  
  $rows = array();
  foreach ($mapping as $country => $language) {
    $rows[] = array(
      $country,
      $countries[$country],
      $language,
      $languages[$language],
      l(
        t('Delete'), 
        'admin/config/regional/language/configure/geoip/delete/' . $country, 
        array('query' => drupal_get_destination())
      ),
    );
  }
  if (count($rows)) {
    $header = array(
      array('data' => t('Country'), 'colspan' => 2),
      array('data' => t('Language'), 'colspan' => 2),
      t('Operations'),
    );
    $output .= theme_table(
      array(
        'header' => $header,
        'rows' => $rows,
        'attributes' => array(),
        'caption' => NULL,
        'colgroups' => NULL,
        'sticky' => TRUE,
        'empty' => t('No GeoIP language mappings defined.'),
      )
    );
  }
  else {
    drupal_set_message(t('No GeoIP language mappings defined.'));
  }
  return $output;
}

/**
 * FAPI callback for creating a new country-language mapping.
 * 
 * @return array
 */
function geoip_language_form($form, &$form_state) {
  $countries = geoip_country_values();
  $mapping = geoip_language_mappings(TRUE);
  $languages = locale_language_list('name', TRUE);
  $options = array();
  foreach ($countries as $key => $value) {
    if (!$mapping[$key]) {
      $options[$key] = "$key - $value";
    }
  }
  
  $form['new'] = array(
    '#type' => 'fieldset',
    '#title' => t('New mapping'),
    '#tree' => 0,
  );
  $form['new']['country'] = array(
    '#type' => 'select',
    '#title' => t('Detected country'),
    '#options' => $options,
  );
  $form['new']['language'] = array(
    '#type' => 'select',
    '#title' => t('Language'),
    '#options' => $languages,
  );

  $form['new']['buttons'] = array();
  $form['new']['buttons']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Add mapping'),
  );
  
  $form['set3']['textfiles'] = array(
    '#type' => 'item',
    '#markup' => theme('geoip_language_mapping_overview', $languages),
  );

  return $form;
}

/**
 * FAPI submit handler.
 * 
 * @return void
 */
function geoip_language_form_submit($form, &$form_state) {
  geoip_language_mapping_create(
    $form_state['values']['country'], 
    $form_state['values']['language']
  );

  $countries = geoip_country_values();
  drupal_set_message(t('GeoIP mapping created for %country.', array('%country' => $countries[$form_state['values']['country']])));

  $form_state['redirect'] = 'admin/config/regional/language/configure/geoip';
}

/**
 * Create th confirm form for deleting a mapping item
 * 
 * @return array
 */
function geoip_admin_delete_mapping(&$form_state, $country) {
  $form['country'] = array(
    '#type' => 'value',
    '#value' => $country,
  );

  return confirm_form(
    $form,
    t('Are you sure you want to delete this mapping?'),
    isset($_GET['destination']) ? $_GET['destination'] : 'admin/config/regional/language/configure/geoip',
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );
}

/**
 * Process a confirmed delete request of a mapping item
 * 
 * @return void
 */
function geoip_admin_delete_mapping_submit($form, &$form_state) {
  $coutryCode = $form_state['values']['country']['build_info']['args'][0];
  geoip_language_mapping_delete($coutryCode);

  $countries = geoip_country_values();  
  drupal_set_message(t('GeoIP mapping deleted for %country.', array('%country' => $countries[$coutryCode])));

  $form_state['redirect'] = 'admin/config/regional/language/configure/geoip';
}