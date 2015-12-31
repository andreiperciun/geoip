<?php

/**
 * @file
 * Contains \Drupal\geoip\Form\GeolocationSettings.
 */

namespace Drupal\geoip\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('geoip.geolocation')
         ->set('plugin_id', $form_state->getValue('plugin_id'))
         ->save();

    parent::submitForm($form, $form_state);
  }

}
