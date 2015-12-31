<?php

/**
 * @file
 * Contains \Drupal\geoip\Plugin\GeoLocator\GeoLocatorBase.
 */

namespace Drupal\geoip\Plugin\GeoLocator;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Plugin\views\PluginBase;

/**
 * Class GeoLocatorBase.
 */
abstract class GeoLocatorBase extends PluginBase implements GeoLocatorInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->getPluginId();
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

}
