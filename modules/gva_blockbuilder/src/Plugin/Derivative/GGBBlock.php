<?php

/**
 * @file
 * Contains \Drupal\gavias_blockbuilder\Derivative\GGBBlock.
 */

namespace Drupal\gavias_blockbuilder\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides blocks which belong to Gavias Blockbuilder.
 */
class GGBBlock extends DeriverBase {
  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $results = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d', array('id', 'title'))
          ->execute();
    foreach ($results as $row) {
      $this->derivatives['gavias_blockbuilder_block____' . $row->id] = $base_plugin_definition;
      $this->derivatives['gavias_blockbuilder_block____' . $row->id]['admin_label'] = 'Gavias Blockbuider ' . $row->title;
    }
    return $this->derivatives;
  }
}
