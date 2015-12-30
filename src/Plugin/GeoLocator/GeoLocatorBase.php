<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 12/29/15
 * Time: 3:58 PM
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

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return (int) $this->pluginDefinition['weight'];
  }
}
