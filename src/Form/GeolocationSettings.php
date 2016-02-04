<?php

/**
 * @file
 * Contains \Drupal\geoip\Form\GeolocationSettings.
 */

namespace Drupal\geoip\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\geoip\Plugin\GeoLocator\GeoIp2WebService;

class GeolocationSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['geoip.geolocation'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'geoip_geolocation_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('geoip.geolocation');

    $form['plugin_id'] = [
      '#type' => 'tableselect',
      '#multiple' => FALSE,
      '#header' => [
        'label' => $this->t('Label'),
        'description' => $this->t('Description'),
      ],
      '#options' => [],
      '#default_value' => $config->get('plugin_id'),
    ];

    foreach (\Drupal::service('plugin.manager.geolocator')->getDefinitions() as $plugin_id => $definition) {
      $form['plugin_id']['#options'][$plugin_id] = [
        'label' => $definition['label'],
        'description' => $definition['description'],
      ];
    }

    if ($config->get('plugin_id') == 'geoip2webservice') {
      $form['webservice'] = array(
        '#type' => 'details',
        '#title' => $this->t('Webservice settings'),
        '#description' => $this->t('Enter your <a href=":maxmind_license_key" target="_blank" title="Get MaxMind License key and User ID">MaxMind Web Services</a> information.',
          array(':maxmind_license_key' => 'https://www.maxmind.com/en/my_license_key')),
        '#open' => TRUE,
      );

      $form['webservice']['user_id'] = array(
        '#type' => 'number',
        '#title' => t('User ID'),
        '#default_value' => $config->get('user_id'),
      );

      $form['webservice']['license_key'] = array(
        '#type' => 'textfield',
        '#title' => t('License Key'),
        '#default_value' => $config->get('license_key'),
        '#description' => t(''),
        '#maxlength' => 20,
        '#size' => 20,
      );

      $form['webservice']['license_type'] = array(
        '#type' => 'select',
        '#title' => t('License Type'),
        '#default_value' => !empty($config->get('license_type')) ? $config->get('license_type') : GeoIp2WebService::LICENSE_TYPE_COUNTRY,
        '#options' => array(
          GeoIp2WebService::LICENSE_TYPE_COUNTRY  => t('Country'),
          GeoIp2WebService::LICENSE_TYPE_CITY     => t('City'),
          GeoIp2WebService::LICENSE_TYPE_INSIGHTS => t('Insights'),
        ),
      );
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('geoip.geolocation')
      ->set('plugin_id', $form_state->getValue('plugin_id'))
      ->set('user_id', $form_state->getValue('user_id'))
      ->set('license_key', $form_state->getValue('license_key'))
      ->set('license_type', $form_state->getValue('license_type'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
