<?php
/**
 * @file
 * Contains \Drupal\shortcode\Shortcode\ShortcodePluginManager
 */

namespace Drupal\gavias_blockbuilder\Shortcode;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a Shortcode plugin manager.
 *
 * @see \Drupal\gavias_blockbuilder\Shortcode\Annotation\Shortcode
 * @see \Drupal\gavias_blockbuilder\Shortcode\ShortcodeInterface
 * @see plugin_api
 */
class ShortcodePluginManager extends DefaultPluginManager {

  /**
   * Constructs a ShortcodePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/gavias_blockbuilder', $namespaces, $module_handler, 'Drupal\gavias_blockbuilder\Plugin\ShortcodeInterface', 'Drupal\gavias_blockbuilder\Annotation\Shortcode');

    // Allow other modules to alter shortcode info via hook_shortcode_info_alter.
    $this->alterInfo('shortcode_info');
    $this->setCacheBackend($cache_backend, 'shortcode_info_plugins');
  }
}