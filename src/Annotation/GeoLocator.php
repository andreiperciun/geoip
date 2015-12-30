<?php
/**
 * Created by PhpStorm.
 * User: mglaman
 * Date: 12/29/15
 * Time: 4:21 PM
 */

namespace Drupal\geoip\Annotation;


use Drupal\Component\Annotation\Plugin;

/**
 * Defines a GeoLocator annotation object.
 *
 * @Annotation
 */
class GeoLocator extends Plugin {

  /**
   * The human-readable name.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * A description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;
}
