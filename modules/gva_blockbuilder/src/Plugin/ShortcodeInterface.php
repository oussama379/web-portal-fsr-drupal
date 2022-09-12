<?php

/**
 * @file
 * Contains \Drupal\filter\Plugin\FilterInterface.
 */

namespace Drupal\gavias_blockbuilder\Plugin;

use Drupal\Core\Language\Language;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the interface for text processing shortcode plugins.
 *
 * @see \Drupal\gavias_blockbuilder\Annotation\Shortcode
 * @see \Drupal\gavias_blockbuilder\ShortCode\ShortcodePluginManager
 * @see \Drupal\gavias_blockbuilder\Plugin\ShortcodeBase
 * @see plugin_api
 */
interface ShortcodeInterface extends ConfigurablePluginInterface, PluginInspectionInterface {


  /**
   * Returns the administrative label for this shortcode plugin.
   *
   * @return string
   */
  public function getLabel();

  /**
   * Returns the administrative description for this shortcode plugin.
   *
   * @return string
   */
  public function getDescription();

  /**
   * Generates a shortcode's settings form.
   *
   * @param array $form
   *   A minimally prepopulated form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the (entire) configuration form.
   *
   * @return array
   *   The $form array with additional form elements for the settings of this
   *   filter. The submitted form values should match $this->settings.
   */
  public function settingsForm(array $form, FormStateInterface $form_state);

  /**
   * Performs the shortcode processing.
   *
   * @param array $attributes
   *   Array of attributes.
   * @param string $text
   *   The text string to be processed.
   * @param string $langcode
   *   The language code of the text to be filtered. Defaults to
   *   LANGCODE_NOT_SPECIFIED.
   *
   * @return string
   *   The processed text.
   */
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED);

  /**
   * Generates a filter's tip.
   *
   * A filter's tips should be informative and to the point. Short tips are
   * preferably one-liners.
   *
   * @param bool $long
   *   Whether this callback should return a short tip to display in a form
   *   (FALSE), or whether a more elaborate filter tips should be returned for
   *   template_preprocess_filter_tips() (TRUE).
   *
   * @return string|null
   *   Translated text to display as a tip, or NULL if this filter has no tip.
   *
   * @todo Split into getSummaryItem() and buildGuidelines().
   */
  public function tips($long = FALSE);

}