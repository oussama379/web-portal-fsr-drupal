<?php

/**
 * @file
 * Contains \Drupal\gavias_sliderlayer\Derivative\GaviasSliderLayerBlock.
 */

namespace Drupal\gavias_sliderlayer\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides blocks which belong to Gavias SliderLayer.
 */
class GaviasSliderLayerBlock extends DeriverBase {
  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    if(!db_table_exists('gavias_sliderlayergroups')){
      return "";
    }
    $results = db_select('{gavias_sliderlayergroups}', 'd')
          ->fields('d', array('id', 'title'))
          ->execute();
    foreach ($results as $row) {
      $this->derivatives['gavias_sliderlayer_block____' . $row->id] = $base_plugin_definition;
      $this->derivatives['gavias_sliderlayer_block____' . $row->id]['admin_label'] = 'Gavias SliderLayer - ' . $row->title;
    }
    return $this->derivatives;
  }
}
