<?php

/**
 * @file
 * Contains \Drupal\field_collection\Controller\FieldCollectionItemController.
 */

namespace Drupal\field_collection\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\field_collection\Entity\FieldCollection;
use Drupal\field_collection\Entity\FieldCollectionItem;
use Drupal\Core\Entity\Controller\EntityViewController;

/**
 * Returns responses for Field collection item routes.
 */
class FieldCollectionItemController extends ControllerBase {

  /**
   * Provides the field collection item submission form.
   *
   * @param \Drupal\field_collection\Entity\FieldCollection $field_collection
   *   The field_collection entity for the field collection item.
   *
   * @param $host_type
   *   The type of the entity hosting the field collection item.
   *
   * @param $host_id
   *   The id of the entity hosting the field collection item.
   *
   * @return array
   *   A field collection item submission form.
   *
   * TODO: additional fields
   */
  public function add(FieldCollection $field_collection, $host_type, $host_id) {
    $host = $this->entityTypeManager()->getStorage($host_type)->load($host_id);
    if (_field_collection_field_item_list_full($host->{$field_collection->id()})) {
      drupal_set_message(t('This field is already full.'), 'error');
      return array('#markup' => 'Can not add to an already full field.');
    }
    else {
      $field_collection_item = $this->entityTypeManager()
        ->getStorage('field_collection_item')
        ->create(array(
          'field_name' => $field_collection->id(),
          'host_type' => $host_type,
          'revision_id' => 0,
        ));

      $form = $this->entityFormBuilder()->getForm($field_collection_item);
      return $form;
    }
  }

  /**
   * Displays a field collection item.
   *
   * @param \Drupal\field_collection\Entity\FieldCollectionItem $field_collection_item
   *   The field collection item we are displaying.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function page(FieldCollectionItem $field_collection_item) {
    $build = $this->buildPage($field_collection_item);
    return $build;
  }

  /**
   * Builds a field collection item page render array.
   *
   * @param \Drupal\field_collection\Entity\FieldCollectionItem $field_collection_item
   *   The field collection item we are displaying.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  protected function buildPage(FieldCollectionItem $field_collection_item) {
    $ret = array('field_collection_items' => $this->entityTypeManager()
      ->getViewBuilder('field_collection_item')
      ->view($field_collection_item));

    return $ret;
  }

  /**
   * The _title_callback for the field_collection_item.view route.
   *
   * @param FieldCollectionItem $field_collection_item
   *   The current field_collection_item.
   *
   * @return string
   *   The page title.
   */
  public function pageTitle(FieldCollectionItem $field_collection_item) {
    return \Drupal::service('entity.repository')->getTranslationFromContext($field_collection_item)->label();
  }

  /**
   * The _title_callback for the field_collection_item.add route.
   *
   * @param \Drupal\field_collection\Entity\FieldCollection $field_collection
   *   The current field collection.
   *
   * @return string
   *   The page title.
   */
  public function addPageTitle(FieldCollection $field_collection) {
    return $this->t('Create @label', array('@label' => $field_collection->label()));
  }

  /**
   * Displays a field collection item revision.
   *
   * @param int $field_collection_item_revision
   *   The field collection item revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($field_collection_item_revision) {
    $field_collection_item = $this->entityTypeManager()
      ->getStorage('field_collection_item')
      ->loadRevision($field_collection_item_revision);

    $field_collection_item_view_controller = new EntityViewController($this->entityManager(), \Drupal::service('renderer'));

    $page = $field_collection_item_view_controller
      ->view($field_collection_item);

    unset($page['field_collection_item'][$field_collection_item->id()]['#cache']);
    return $page;
  }

  /**
   * Page title callback for a field collection item revision.
   *
   * @param int $field_collection_item_revision
   *   The field collection item revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($field_collection_item_revision) {
    $field_collection_item = $this->entityTypeManager()
      ->getStorage('field_collection_item')
      ->loadRevision($field_collection_item_revision);

    return $this->t('Revision %revision of %title', array('%revision' => $field_collection_item_revision, '%title' => $field_collection_item->label()));
  }

}
