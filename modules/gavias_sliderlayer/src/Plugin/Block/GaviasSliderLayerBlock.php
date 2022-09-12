<?php

/**
 * @file
 * Contains \Drupal\gavias_sliderlayer\Plugin\Block\GaviasSliderLayerBlock.
 */

namespace Drupal\gavias_sliderlayer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides blocks which belong to Gavias Slider.
 *
 *
 * @Block(
 *   id = "gavias_sliderlayer_block",
 *   admin_label = @Translation("Gavias SliderLayer"),
 *   category = @Translation("Gavias Slider"),
 *   deriver = "Drupal\gavias_sliderlayer\Plugin\Derivative\GaviasSliderLayerBlock",
 * )
 *
 */

class GaviasSliderLayerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $sid = $this->getDerivativeId();

     $block = array();
      if (str_replace('gavias_sliderlayer_block____', '', $sid) != $sid) {
        $sid = str_replace('gavias_sliderlayer_block____', '', $sid);

        $content_block = gavias_sliderlayer_block_content($sid);
        
        if(!$content_block) $content_block =  'No block builder selected';
        $block = array(
          '#theme' => 'block-slider',
          '#content' => $content_block,
          '#cache' => array('max-age' => 0)
        );
      }

      return $block;
  }


  /**
   *  Default cache is disabled. 
   * 
   * @param array $form
   * @param \Drupal\gavias_sliderlayer\Plugin\Block\FormStateInterface $form_state
   * @return 
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $rebuild_form = parent::buildConfigurationForm($form, $form_state);
    return $rebuild_form;
  }

}
