<?php

/**
 * @file
 * Contains \Drupal\gavias_views_magazine\Plugin\views\style\mlayout_1.
 */

namespace Drupal\gavias_views_magazine\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
/**
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "mlayout_1",
 *   title = @Translation("Magazine Layout #1"),
 *   help = @Translation("Magazine Layout #1: Large post & columns small posts"),
 *   theme = "views_view_mlayout_1",
 *   display_types = {"normal"}
 * )
 */
class mlayout_1 extends StylePluginBase {

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
