<?php

/**
 * @file
 * Contains \Drupal\gavias_view\Plugin\views\style\GvaOwl.
 */

namespace Drupal\gavias_view\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
/**
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "gvaonefour",
 *   title = @Translation("Gavias One Four Items"),
 *   help = @Translation("Displays items as Grid One Four Items."),
 *   theme = "views_view_gvaonefour",
 *   display_types = {"normal"}
 * )
 */
class gvaonefour extends StylePluginBase {

  /**
   * Does the style plugin allows to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Set default options
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $settings = gavias_view_owl_default_settings();
    foreach ($settings as $k => $v) {
      $options[$k] = array('default' => $v);
    }
    return $options;
  }

  /**
   * Render the given style.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['el_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Extra class name'),
      '#default_value' => $this->options['el_class'],
    );
    $form['el_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Extra id name'),
      '#default_value' => $this->options['el_id'],
    );
  }
}
