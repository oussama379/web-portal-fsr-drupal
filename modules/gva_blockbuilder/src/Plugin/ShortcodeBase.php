<?php

/**
 * @file
 * Contains \Drupal\gavias_blockbuilder\Plugin\ShortcodeBase.
 */

namespace Drupal\gavias_blockbuilder\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;

/**
 * Provides a base class for Shortcode plugins.
 *
 * @see \Drupal\filter\Annotation\Filter
 * @see \Drupal\gavias_blockbuilder\ShortcodePluginManager
 * @see \Drupal\gavias_blockbuilder\Plugin\ShortcodeInterface
 * @see plugin_api
 */
abstract class ShortcodeBase extends PluginBase implements ShortcodeInterface {

  /**
   * The plugin ID of this filter.
   *
   * @var string
   */
  protected $plugin_id;

  /**
   * The name of the provider that owns this filter.
   *
   * @var string
   */
  public $provider;

  /**
   * A Boolean indicating whether this filter is enabled.
   *
   * @var bool
   */
  public $status = FALSE;

  /**
   * An associative array containing the configured settings of this filter.
   *
   * @var array
   */
  public $settings = array();

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->provider = $this->pluginDefinition['provider'];

    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array(
      'id' => $this->getPluginId(),
      'provider' => $this->pluginDefinition['provider'],
      'status' => $this->status,
      'settings' => $this->settings,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    if (isset($configuration['status'])) {
      $this->status = (bool) $configuration['status'];
    }
    if (isset($configuration['settings'])) {
      $this->settings = (array) $configuration['settings'];
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'provider' => $this->pluginDefinition['provider'],
      'status' => FALSE,
      'settings' => $this->pluginDefinition['settings'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->pluginDefinition['type'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['title'];
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
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // Implementations should work with and return $form. Returning an empty
    // array here allows the text format administration form to identify whether
    // this shortcode plugin has any settings form elements.
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
  }


  /**
   * Combines user attributes with known attributes.
   *
   * The $defaults should be considered to be all of the attributes which are
   * supported by the caller and given as a list. The returned attributes will
   * only contain the attributes in the $defaults list.
   *
   * If the $attributes list has unsupported attributes, they will be ignored and
   * removed from the final return list.
   *
   * @param array $defaults
   *   Entire list of supported attributes and their defaults.
   *
   * @param array $attributes
   *   User defined attributes in Shortcode tag.
   *
   * @return array
   *   Combined and filtered attribute list.
   */
  public function getAttributes($defaults, $attributes) {
    $attributes = (array) $attributes;
    $out = array();
    foreach ($defaults as $name => $default) {
      if (array_key_exists($name, $attributes)) {
        $out[$name] = $attributes[$name];
      }
      else {
        $out[$name] = $default;
      }
    }
    return $out;
  }

  /**
   * Add a class into a classes string if not already inside.
   *
   * @param mixed|string|array $classes
   *   The classes string or array.
   *
   * @param string $new_class
   *   The class to add.
   *
   * @return string
   *   The proper classes string.
   */
  public function addClass($classes = '', $new_class = '') {
    if ($classes) {
      if (!is_array($classes)) {
        $classes = explode(' ', $classes);
      }
      array_unshift($classes, $new_class);
      $classes = array_unique($classes);
    }
    else {
      $classes[] = $new_class;
    }
    return implode(' ', $classes);
  }

  /**
   * Returns a url to be used in a link element given path and url.
   *
   * @param string $path
   *   The internal path to be translated.
   * @param string $url
   *   The url.
   * @param bool $url_overrides_path
   *   TRUE if $url should override $path.
   */
  public function getUrlFromPath($path, $url = NULL, $url_overrides_path = TRUE) {

    if (!empty($url)) {
      if ($url_overrides_path) {
        return $url;
      }
    }

    if ($path === '<front>') {
      $path = '/';
    }
    else {
      $path = '/' . ltrim($path, '/');
    }

    //$path = Url::fromUserInput($path);

    /** @var \Drupal\Core\Path\AliasManager $alias_manager */
    $alias_manager = \Drupal::service('path.alias_manager');
    $alias = $alias_manager->getAliasByPath($path);

    return $alias;
  }

  /**
   * Returns a suitable title string given the user provided title and test.
   *
   * @param string $title
   *   The user provided title.
   * @param string $text
   *   The user provided text.
   *
   * @return string
   *   The title to be used.
   */
  public function getTitleFromAttributes($title, $text) {

    // Allow setting no title.
    if ($title === '<none>') {
      $title = '';
    }
    else {
      $title = empty($title) ? Html::escape($text) : Html::escape($title);
    }

    return $title;
  }

  /**
   * Wrapper for renderPlain.
   *
   * We use renderplain so that the shortcode's cache tags would not bubble up
   * to the parent and affect cacheability. Shortcode should be part of content
   * and self-container.
   *
   * @param $element
   * @return \Drupal\Component\Render\MarkupInterface|mixed
   */
  public function render(&$element) {
    /** @var Renderer $renderer */
    $renderer = \Drupal::service('renderer');
    return $renderer->renderPlain($element);
  }

}