<?php

/**
 * @file
 * Contains \Drupal\gavias_view\Plugin\views\style\gvagrid.
 */

namespace Drupal\gavias_view\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
/**
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "gvagrid",
 *   title = @Translation("Gavias Grid"),
 *   help = @Translation("Displays Items as Grid."),
 *   theme = "views_view_gvagrid",
 *   display_types = {"normal"}
 * )
 */
class gvagrid extends StylePluginBase {

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
    $form['items_lg'] = array(
      '#type' => 'select',
      '#title' => $this->t('Columns for Large Screen.'),
      '#options' => array('1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6),
      '#default_value' => isset($this->options['items_lg']) && $this->options['items_lg'] ? $this->options['items_lg'] : 3,
    );
    $form['items_md'] = array(
      '#type' => 'select',
      '#title' => $this->t('Columns for Medium Screen.'),
      '#options' => array('1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6),
      '#default_value' => isset($this->options['items_md']) && $this->options['items_md'] ? $this->options['items_md'] : 3,
    );
    $form['items_sm'] = array(
      '#type' => 'select',
      '#title' => $this->t('Columns for Small Screen.'),
      '#options' => array('1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6),
      '#default_value' => isset($this->options['items_sm']) && $this->options['items_sm'] ? $this->options['items_sm'] : 2,
    );
    $form['items_xs'] = array(
      '#type' => 'select',
      '#title' => $this->t('Columns for Extra Small Screen (Mobile).'),
      '#options' => array('1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6),
      '#default_value' => isset($this->options['items_xs']) && $this->options['items_xs'] ? $this->options['items_xs'] : 2,
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
