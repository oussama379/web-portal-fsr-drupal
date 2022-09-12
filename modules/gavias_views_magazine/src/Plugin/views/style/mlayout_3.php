<?php

/**
 * @file
 * Contains \Drupal\gavias_views_magazine\Plugin\views\style\mlayout_3.
 */

namespace Drupal\gavias_views_magazine\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
/**
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "mlayout_3",
 *   title = @Translation("Magazine Layout #3"),
 *   help = @Translation("Magazine Layout #3: Post Large with Carousel Small Posts"),
 *   theme = "views_view_mlayout_3",
 *   display_types = {"normal"}
 * )
 */
class mlayout_3 extends StylePluginBase {

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
    $form['details'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Settings Carousel'),
      '#description' => $this->t('Settings Carousel for Small Items'),
    );
    $form['items'] = array(
      '#type' => 'select',
      '#title' => $this->t('Items'),
      '#description' => $this->t('Number Items Show.'),
      '#default_value' => isset($this->options['items']) && $this->options['items'] ? $this->options['items'] : 3,
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
    );
    $form['items_lg'] = array(
      '#type' => 'select',
      '#title' => $this->t('Nubmer Items for Desktop'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => isset($this->options['items_lg']) && $this->options['items_lg'] ? $this->options['items_lg'] : 3,
    );
    $form['items_md'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number Items for Desktop Small'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => isset($this->options['items_md']) && $this->options['items_md'] ? $this->options['items_md'] : 3,
    );
    $form['items_sm'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number Items for Tablet'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => isset($this->options['items_sm']) && $this->options['items_sm'] ? $this->options['items_sm'] : 2,
    );
    $form['items_xs'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number Items for Mobile'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => isset($this->options['items_xs']) && $this->options['items_xs'] ? $this->options['items_xs'] : 2,
    );
    $form['loop'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Loop'),
      '#default_value' => isset($this->options['loop']) && $this->options['loop'] ? true : false,
    );
    $form['speed'] = array(
      '#type' => 'number',
      '#title' => $this->t('Slide Speed'),
      '#default_value' => isset($this->options['speed']) && $this->options['speed'] ? $this->options['speed'] : 200,
      '#description' => $this->t('Slide speed in milliseconds.'),
    );
    $form['auto_play'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('AutoPlay'),
      '#default_value' => isset($this->options['auto_play']) && $this->options['auto_play'] ? true : false,
    );
    $form['auto_play_speed'] = array(
      '#type' => 'number',
      '#title' => $this->t('Auto Play Speed'),
      '#default_value' => isset($this->options['auto_play_speed']) && $this->options['auto_play_speed'] ? $this->options['auto_play_speed'] : 1000,
      '#description' => $this->t('Speed for auto play.'),
    );
     $form['auto_play_timeout'] = array(
      '#type' => 'number',
      '#title' => $this->t('Auto Play TimeOut'),
      '#default_value' => isset($this->options['auto_play_timeout']) && $this->options['auto_play_timeout'] ? $this->options['auto_play_timeout'] : 3000,
      '#description' => $this->t('TimeOut for auto play.'),
    );
    $form['auto_play_hover'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Auto Play Hover Pause'),
      '#default_value' => isset($this->options['auto_play_hover']) && $this->options['auto_play_hover'] ? true : false,
    );
     
    $form['navigation'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Navigation'),
      '#default_value' => isset($this->options['navigation']) && $this->options['navigation'] ? true : false,
    );
    $form['rewind_nav'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Rewind Nav'),
      '#default_value' => isset($this->options['rewind_nav']) && $this->options['rewind_nav'] ? true : false,
      '#description' => $this->t('Slide to first item.'),
    );
    $form['pagination'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Pagination'),
      '#default_value' => isset($this->options['pagination']) && $this->options['pagination'] ? true : false,
      '#description' => $this->t('Show pagination.'),
    );
    $form['mouse_drag'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Mouse Drag'),
      '#default_value' => isset($this->options['mouse_drag']) && $this->options['mouse_drag'] ? true : false,
      '#description' => $this->t('Turn off/on mouse events.'),
    );
    $form['touch_drag'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Touch Drag'),
      '#default_value' => isset($this->options['touch_drag']) && $this->options['touch_drag'] ? true : false,
      '#description' => $this->t('Turn off/on touch events.'),
    );
  }
}
