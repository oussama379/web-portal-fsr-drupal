<?php

/**
 * @file
 * Contains \Drupal\gavias_views_magazine\Plugin\views\style\mlayout_5.
 */

namespace Drupal\gavias_views_magazine\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
/**
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "mlayout_6",
 *   title = @Translation("Magazine Layout #5"),
 *   help = @Translation("Magazine Layout #5: Large post & 2 columns small posts"),
 *   theme = "views_view_mlayout_6",
 *   display_types = {"normal"}
 * )
 */
class mlayout_6 extends StylePluginBase {

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
    $form['number_post_column'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number posts in small column.'),
      '#options' => array('1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6, '7'=>7, '8'=>8, '9'=>9, '10'=>10),
      '#default_value' => isset($this->options['number_post_column']) && $this->options['number_post_column'] ? $this->options['number_post_column'] : 4,
    );
    
    $form['el_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Extra class name'),
      '#default_value' =>  isset($this->options['el_class']) ? $this->options['el_class'] : ''
    );
    $form['el_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Extra id name'),
      '#default_value' => isset($this->options['el_id']) ? $this->options['el_id'] : '',
    );
  }
}
