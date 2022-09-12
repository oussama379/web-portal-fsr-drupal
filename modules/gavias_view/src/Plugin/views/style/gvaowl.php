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
 *   id = "gvaowl",
 *   title = @Translation("Gavias Owl Carousel"),
 *   help = @Translation("Displays items as Owl Carousel."),
 *   theme = "views_view_gvaowl",
 *   display_types = {"normal"}
 * )
 */
class gvaowl extends StylePluginBase {

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

    $form['items'] = array(
      '#type' => 'select',
      '#title' => $this->t('Items'),
      '#description' => $this->t('Number Items Show.'),
      '#default_value' => $this->options['items'],
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
    );
    $form['items_lg'] = array(
      '#type' => 'select',
      '#title' => $this->t('Nubmer Items for Desktop'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => $this->options['items_lg'],
    );
    $form['items_md'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number Items for Desktop Small'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => $this->options['items_md'],
    );
    $form['items_sm'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number Items for Tablet'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => $this->options['items_sm'],
    );
    $form['items_xs'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number Items for Mobile'),
      '#options' => array('1'=> 1, '2'=> 2, '3'=>3, '4'=> 4, '5'=> 5, '6'=> 6, '7'=> 7, '8'=> 8),
      '#default_value' => $this->options['items_xs'],
    );
    $form['loop'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Loop'),
      '#default_value' => $this->options['loop']
    );
    $form['speed'] = array(
      '#type' => 'number',
      '#title' => $this->t('Slide Speed'),
      '#default_value' => $this->options['speed'],
      '#description' => $this->t('Slide speed in milliseconds.'),
    );
    $form['auto_play'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('AutoPlay'),
      '#default_value' => $this->options['auto_play'],
    );
    $form['auto_play_speed'] = array(
      '#type' => 'number',
      '#title' => $this->t('Auto Play Speed'),
      '#default_value' => $this->options['auto_play_speed'],
      '#description' => $this->t('Speed for auto play.'),
    );
     $form['auto_play_timeout'] = array(
      '#type' => 'number',
      '#title' => $this->t('Auto Play TimeOut'),
      '#default_value' => $this->options['auto_play_timeout'],
      '#description' => $this->t('TimeOut for auto play.'),
    );
    $form['auto_play_hover'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Auto Play Hover Pause'),
      '#default_value' => $this->options['auto_play_hover'],
    );
     
    $form['navigation'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Navigation'),
      '#default_value' => $this->options['navigation']
    );
    $form['rewind_nav'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Rewind Nav'),
      '#default_value' => $this->options['rewind_nav'],
      '#description' => $this->t('Slide to first item.'),
    );
    $form['pagination'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Pagination'),
      '#default_value' => $this->options['pagination'],
      '#description' => $this->t('Show pagination.'),
    );
    $form['mouse_drag'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Mouse Drag'),
      '#default_value' => $this->options['mouse_drag'],
      '#description' => $this->t('Turn off/on mouse events.'),
    );
    $form['touch_drag'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Touch Drag'),
      '#default_value' => $this->options['touch_drag'],
      '#description' => $this->t('Turn off/on touch events.'),
    );
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
