<?php
/**
 * @file
 * Contains \Drupal\gavias_blockbuilder\Shortcode\ShortcodeService
 */

namespace Drupal\gavias_blockbuilder\Shortcode;

use Drupal\filter\Plugin\FilterInterface;
use Drupal\Core\Language\Language;
use Drupal\Component\Plugin\PluginManagerInterface;

class ShortcodeService {

  /**
   * Returns array of shortcode plugin definitions enabled for the filter.
   *
   * @param FilterInterface $filter
   *   The filter. Defaults to NULL, where all shortcode plugins will be
   *   returned.
   *
   * @param bool $reset
   *   TRUE if the static cache should be reset. Defaults to FALSE.
   *
   * @return array
   *   Array of shortcode plugin definitions.
   */
  function getShortcodePlugins(FilterInterface $filter = NULL, $reset = FALSE) {
    $shortcodes = &drupal_static(__FUNCTION__);

    if (!isset($shortcodes) || $reset) {
      /** @var PluginManagerInterface $type */
      $type = \Drupal::service('plugin.manager.gbbshortcode');

      $definitions_raw = $type->getDefinitions();
      $definitions = array();
      foreach ($definitions_raw as $definition) {
        $definitions[$definition['id']] = $definition;
      }

      // Alteration of the ShortCode plugin definitions should utilize
      // plugin manager's $alterHook, instead of D7's drupal_alter.
      //drupal_alter('shortcode_info', $definitions);

      $shortcodes = array(
        'plugins' => $definitions,
        'filters' => array(),
      );
    }

    // If filter is given, only return plugin definitions enabled on the filter.
    if ($filter) {
      $filter_id = $filter->getPluginId();
      if (!isset($shortcodes['filters'][$filter_id])) {
        $settings = $filter->settings;


        $enabled_shortcodes = array();
        foreach ($settings as $shortcode_id => $status) {
          if ($status && isset($shortcodes['plugins'][$shortcode_id])) {
            $enabled_shortcodes[$shortcode_id] = $shortcodes['plugins'][$shortcode_id];
          }
        }
        $shortcodes['filters'][$filter_id] = $enabled_shortcodes;
      }

      return $shortcodes['filters'][$filter_id];
    }

    // Return all defined shortcode plugin definitions.
    return $shortcodes['plugins'];

  }

  /**
   * Creates shortcode plugin instance or loads from static cache.
   *
   * @param string $shortcode_id
   *   The shortcode plugin id.
   *
   * @return \Drupal\shortcode\Plugin\ShortcodeInterface
   *   The plugin instance.
   */
  function getShortcodePlugin($shortcode_id) {
    $plugins = &drupal_static(__FUNCTION__, array());
    if (!isset($plugins[$shortcode_id])) {

      /** @var \Drupal\shortcode\Shortcode\ShortcodePluginManager $type */
      $type = \Drupal::service('plugin.manager.gbbshortcode');

      $plugins[$shortcode_id] = $type->createInstance($shortcode_id);
    }
    return $plugins[$shortcode_id];
  }

}