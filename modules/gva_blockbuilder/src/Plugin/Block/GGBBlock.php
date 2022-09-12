<?php

/**
 * @file
 * Contains \Drupal\gavias_blockbuilder\Plugin\Block\GGBBlock.
 */

namespace Drupal\gavias_blockbuilder\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides blocks which belong to Gavias Blockbuilder.
 *
 *
 * @Block(
 *   id = "gavias_blockbuilder_block",
 *   admin_label = @Translation("Gavias Blockbuilder"),
 *   category = @Translation("Gavias Blockbuilder"),
 *   deriver = "Drupal\gavias_blockbuilder\Plugin\Derivative\GGBBlock",
 * )
 *
 */

class GGBBlock extends BlockBase {

  protected $bid;

  /**
   * {@inheritdoc}
   */
  public function build() {
    $bid = $this->getDerivativeId();
    $this->bid = $bid;
     $block = array();
      if (str_replace('gavias_blockbuilder_block____', '', $bid) != $bid) {
        $bid = str_replace('gavias_blockbuilder_block____', '', $bid);
        $results = gavias_blockbuilder_load($bid);
        if(!$results) return 'No block builder selected';
        $content_block = gavias_blockbuilder_frontend($results->params);
        $user = \Drupal::currentUser();
        $url = \Drupal::request()->getRequestUri();
        $edit_url = '';
        if($user->hasPermission('administer gaviasblockbuilder')){
          $edit_url = \Drupal::url('gavias_blockbuilder.admin.edit', array('bid' => $bid, 'destination' =>  $url));
        }
        //print $content_block;
        $block = array(
          '#theme' => 'block-builder',
          '#content' => $content_block,
          '#edit_url' => $edit_url,
          '#cache' => array('max-age' => 0)
        );
      }

      return $block;
  }
  /**
   *  Default cache is disabled. 
   * 
   * @param array $form
   * @param \Drupal\gavias_blockbuilder\Plugin\Block\FormStateInterface $form_state
   * @return 
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $rebuild_form = parent::buildConfigurationForm($form, $form_state);
    $rebuild_form['cache']['max_age']['#default_value'] = 0;
    return $rebuild_form;
  }
}
