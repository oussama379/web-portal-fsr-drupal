<?php

/**
 * @file
 * Contains \Drupal\field_collection\Plugin\Field\FieldType\FieldCollection.
 */

namespace Drupal\field_collection\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field_collection\Entity\FieldCollectionItem;

/**
 * Plugin implementation of the 'field_collection' field type.
 *
 * @FieldType(
 *   id = "field_collection",
 *   label = @Translation("Field collection"),
 *   description = @Translation(
 *     "This field stores references to embedded entities, which itself may
 *     contain any number of fields."
 *   ),
 *   settings = {
 *     "path" = "",
 *     "hide_blank_items" = TRUE,
 *   },
 *   instance_settings = {
 *   },
 *   default_widget = "field_collection_embed",
 *   default_formatter = "field_collection_list"
 * )
 */
class FieldCollection extends FieldItemBase {

  /**
   * Cache for whether the host is a new revision.
   *
   * Set in preSave and used in update().  By the time update() is called
   * isNewRevision() for the host is always FALSE.
   *
   * @var bool
   */
  protected $newHostRevision;

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'int',
          'not null' => TRUE
        ),
        'revision_id' => array(
          'type' => 'int',
          'not null' => FALSE
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Field collection item ID'))
      ->setSetting('unsigned', TRUE)
      ->setReadOnly(TRUE);

    $properties['revision_id'] = DataDefinition::create('integer')
      ->setLabel(t('Field collection item revision'))
      ->setSetting('unsigned', TRUE)
      ->setReadOnly(TRUE);

    return $properties;
  }

  public function getFieldCollectionItem($create = FALSE) {
    if (isset($this->field_collection_item)) {
      return $this->field_collection_item;
    }
    elseif (isset($this->value)) {
      // By default always load the default revision, so caches get used.
      $field_collection_item = FieldCollectionItem::load($this->value);
      if ($field_collection_item !== NULL && $field_collection_item->getRevisionId() != $this->revision_id) {
        // A non-default revision is a referenced, so load this one.
        $field_collection_item = \Drupal::entityTypeManager()->getStorage('field_collection_item')->loadRevision($this->revision_id);
      }
      return $field_collection_item;
    }
    elseif ($create) {
      $field_collection_item = FieldCollectionItem::create(['field_name' => $this->getFieldDefinition()->getName()]);

      // TODO: Uncomment or delete
      /*
      $field_collection_item->setHostEntity($this->getEntity(), FALSE);
      */

      return $field_collection_item;
    }
    return FALSE;
  }

  public function delete() {
    $field_collection_item = $this->getFieldCollectionItem();
    // Set a flag to remember that the host entity is being deleted. See
    // \Drupal\field_collection\Entity\FieldCollectionItem::deleteHostEntityReference().
    if ($field_collection_item !== NULL) {
      $field_collection_item->field_collection_deleting = TRUE;
      $field_collection_item->delete();
    }
    parent::delete();
  }

  // TODO: Format comment
  /**
   * Care about removed field collection items.
   *
   * Support saving field collection items in @code $item['entity'] @endcode. This
   * may be used to seamlessly create field collection items during host-entity
   * creation or to save changes to the host entity and its collections at once.
   */
  public function preSave() {

    if ($field_collection_item = $this->getFieldCollectionItem()) {
      // TODO: Handle node cloning
      /*
      if (!empty($host_entity->is_new) && empty($entity->is_new)) {
        // If the host entity is new but we have a field_collection that is not
        // new, it means that its host is being cloned. Thus we need to clone
        // the field collection entity as well.
        $new_entity = clone $entity;
        $new_entity->item_id = NULL;
        $new_entity->revision_id = NULL;
        $new_entity->is_new = TRUE;
        $entity = $new_entity;
      }
      */

      // TODO: Handle deleted items
      /*
      $field_name = $this->getFieldDefinition()->field_name;
      $host_original = $host->original;
      $items_original = !empty($host_original->$field_name) ? $host_original->$field_name : array();
      $original_by_id = array_flip(field_collection_field_item_to_ids($items_original));
      foreach ($items as &$item) {
      */


      // TODO: Handle deleted items
      /*
        unset($original_by_id[$item['value']]);
      }
      // If there are removed items, care about deleting the item entities.
      if ($original_by_id) {
        $ids = array_flip($original_by_id);
        // If we are creating a new revision, the old-items should be kept but get
        // marked as archived now.
        if (!empty($host_entity->revision)) {
          db_update('field_collection_item')
            ->fields(array('archived' => 1))
            ->condition('item_id', $ids, 'IN')
            ->execute();
        }
        else {
          // Delete unused field collection items now.
          foreach (FieldCollectionItem::loadMultiple($ids) as $un_item) {
            $un_item->updateHostEntity($host_entity);
            $un_item->deleteRevision(TRUE);
          }
        }
      }
      */

      $this->newHostRevision = $this->getEntity()->isNewRevision();

      // If the host entity is saved as new revision, do the same for the item.
      if ($this->newHostRevision) {
        $host = $this->getEntity();

        $field_collection_item->setNewRevision();

        // TODO: Verify for D8, may not be necessary
        /*
        // Without this cache clear entity_revision_is_default will
        // incorrectly return false here when creating a new published revision
        if (!isset($cleared_host_entity_cache)) {
          list($entity_id) = entity_extract_ids($host_entity_type, $host_entity);
          entity_get_controller($host_entity_type)->resetCache(array($entity_id));
          $cleared_host_entity_cache = true;
        }
        */

        if ($host->isDefaultRevision()) {
          $field_collection_item->isDefaultRevision(TRUE);
          //$entity->archived = FALSE;
        }
      }

      if ($field_collection_item->isNew()) {
        $field_collection_item->setHostEntity($this->getEntity(), FALSE);
      }

      $field_collection_item->save(TRUE);
      $this->value = $field_collection_item->id();
      $this->revision_id = $field_collection_item->getRevisionId();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if ($this->value) {
      return FALSE;
    }
    else if ($this->getFieldCollectionItem()) {
      return $this->getFieldCollectionItem()->isEmpty();
    }
    return TRUE;
  }

}

