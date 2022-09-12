<?php

/**
 * Contains \Drupal\node\FieldCollectionListController.
 */

namespace Drupal\field_collection;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of field collections.
 */
class FieldCollectionListBuilder extends ConfigEntityListBuilder {
  // TODO: Add "Used in" column
  // $rows[$field_name]['data'][2] = l(t('manage fields'), 'admin/structure/field-collections/' . $field_name_url_str . '/fields');

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = array(
      'data' => $entity->label(),
      'class' => array('menu-label'),
    );
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = array('title' => $this->t('Machine name'));
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['#empty'] = t('No field collections have been defined yet. To do so attach a field collection field to any entity.');
    return $build;
  }

}
