<?php

/**
 * @file
 * Definition of \Drupal\field_collection\Entity\FieldCollectionItem.
 */

namespace Drupal\field_collection\Entity;

use Drupal\Core\Database\Database;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\field_collection\FieldCollectionItemInterface;

/**
 * Defines the field collection item entity class.
 *
 * @ContentEntityType(
 *   id = "field_collection_item",
 *   label = @Translation("Field Collection Item"),
 *   bundle_label = @Translation("Field Name"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "access" = "Drupal\field_collection\FieldCollectionItemAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\field_collection\FieldCollectionItemForm",
 *       "edit" = "Drupal\field_collection\FieldCollectionItemForm",
 *       "delete" = "Drupal\field_collection\Form\FieldCollectionItemDeleteForm"
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   base_table = "field_collection_item",
 *   revision_table = "field_collection_item_revision",
 *   fieldable = TRUE,
 *   translatable = FALSE,
 *   render_cache = FALSE,
 *   entity_keys = {
 *     "id" = "item_id",
 *     "revision" = "revision_id",
 *     "bundle" = "field_name",
 *     "label" = "field_name",
 *     "uuid" = "uuid"
 *   },
 *   bundle_keys = {
 *     "bundle" = "field_name"
 *   },
 *   bundle_entity_type = "field_collection",
 *   field_ui_base_route = "entity.field_collection.edit_form",
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "/field_collection_item/{field_collection_item}",
 *     "delete-form" = "/field_collection_item/{field_collection_item}",
 *     "edit-form" = "/field_collection_item/{field_collection_item}/edit"
 *   }
 * )
 */
class FieldCollectionItem extends ContentEntityBase implements FieldCollectionItemInterface {

  // TODO: Should references to $this->host_type (a base field) use a getter?

  /**
   * The id of the host entity.
   *
   * TODO: Possibly convert it to a FieldInterface.
   */
  protected $host_id;

  /**
   * Implements Drupal\Core\Entity\EntityInterface::id().
   */
  public function id() {
    return $this->item_id->value;
  }

  /**
   * Overrides \Drupal\Core\Entity\label().
   */
  public function label() {
    $field_label = $this->getHost()
      ->getFieldDefinition($this->bundle())
      ->label();

    if (empty($field_label)) {
      return parent::label();
    }
    else {
      return t('@label @delta of @host',
               array('@label' => $field_label,
                     '@delta' => $this->getDelta(),
                     '@host' => $this->getHost()->label()));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['item_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Field collection item ID'))
      ->setDescription(t('The field collection item ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['host_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Host\'s entity type'))
      ->setDescription(
        t('Type of entity for the field collection item\'s host.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The field collection item UUID.'))
      ->setReadOnly(TRUE);

    $fields['revision_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The field collection item revision ID.'))
      ->setReadOnly(TRUE);

    $fields['field_name'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The field collection item field.'))
      ->setSetting('target_type', 'field_collection')
      ->setReadOnly(TRUE);

    return $fields;
  }

  /**
   * Save the field collection item.
   *
   * By default, always save the host entity, so modules are able to react
   * upon changes to the content of the host and any 'last updated' dates of
   * entities get updated.
   *
   * For creating an item a host entity has to be specified via setHostEntity()
   * before this function is invoked. For the link between the entities to be
   * fully established, the host entity object has to be updated to include a
   * reference on this field collection item during saving. So do not skip
   * saving the host for creating items.
   *
   * @param $skip_host_save
   *   (internal) If TRUE is passed, the host entity is not saved automatically
   *   and therefore no link is created between the host and the item or
   *   revision updates might be skipped. Use with care.
   */
  public function save($skip_host_save = FALSE) {
    /* TODO: Need this.
    // Make sure we have a host entity during creation.
    if (!empty($this->is_new) && !(isset($this->hostEntityId) || isset($this->hostEntity) || isset($this->hostEntityRevisionId))) {
      throw new Exception("Unable to create a field collection item without a given host entity.");
    }
    */

    // Only save directly if we are told to skip saving the host entity. Else,
    // we always save via the host as saving the host might trigger saving
    // field collection items anyway (e.g. if a new revision is created).
    if ($skip_host_save) {
      return parent::save();
    }
    else {
      $host_entity = $this->getHost();
      if (!$host_entity) {
        throw new \Exception('Unable to save a field collection item without a valid reference to a host entity');
      }

      /* TODO: Need this.
      // If this is creating a new revision, also do so for the host entity.
      if (!empty($this->revision) || !empty($this->is_new_revision)) {
        $host_entity->revision = TRUE;
        if (!empty($this->default_revision)) {
          entity_revision_set_default($this->hostEntityType, $host_entity);
        }
      }
      */

      // Set the host entity reference, so the item will be saved with the host.
      // @see field_collection_field_presave()
      $delta = $this->getDelta();
      $value = $host_entity->{$this->bundle()}->getValue();
      if (isset($delta)) {
        $value[$delta] = array('field_collection_item' => $this);
      }
      else {
        $value[] = array('field_collection_item' => $this);
      }
      $host_entity->{$this->bundle()}->setValue($value);

      return $host_entity->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    if ($this->getHost()) {
      $this->deleteHostEntityReference();
    }
    parent::delete();
  }

  /**
   * Overrides \Drupal\Core\Entity\Entity::createDuplicate().
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    $duplicate->revision_id->value = NULL;
    $duplicate->id->value = NULL;
    return $duplicate;
  }

  /**
   * Deletes the host entity's reference of the field collection item.
   */
  protected function deleteHostEntityReference() {
    $delta = $this->getDelta();
    if ($this->id() && isset($delta) && NULL !== $this->getHost() && isset($this->getHost()->{$this->bundle()}[$delta])) {
      $host = $this->getHost();
      unset($host->{$this->bundle()}[$delta]);
      // Do not save when the host entity is being deleted. See
      // \Drupal\field_collection\Plugin\Field\FieldType\FieldCollection::delete().
      if (empty($host->field_collection_deleting)) {
        $host->save();
      }
    }
  }

  /**
   * Overrides \Drupal\Core\Entity\Entity::getRevisionId().
   */
  public function getRevisionId() {
    return $this->revision_id->value;
  }

  /**
   * Overrides \Drupal\Core\Entity\Entity::uri().
   */
  public function uri() {
    $ret = array(
      'path' => 'field-collection-item/' . $this->id(),
      'options' => array(
        'entity_type' => $this->entityType,
        'entity' => $this,
      )
    );

    return $ret;
  }

  /**
   * {@inheritdoc}
   */
  public function getDelta() {
    $host = $this->getHost();

    if (($host = $this->getHost()) && isset($host->{$this->bundle()})) {
      foreach ($host->{$this->bundle()} as $delta => $item) {
        if (isset($item->value) && $item->value == $this->id()) {
          return $delta;
        }
        elseif (isset($item->field_collection_item) && $item->field_collection_item === $this) {
          return $delta;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getHost($reset = FALSE) {
    if ($id = $this->getHostId()) {
      $storage = $this->entityTypeManager()->getStorage($this->host_type->value);
      if ($reset) {
        $storage->resetCache([$id]);
      }
      return $storage->load($id);
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getHostId() {
    if (!isset($this->host_id)) {
      $entity_info = $this->entityTypeManager()->getDefinition($this->host_type->value, TRUE);
      $table = $entity_info->get('base_table') . '__' . $this->bundle();

      if (Database::getConnection()->schema()->tableExists($table)) {
        // @todo This is not how you interpolate variables into a db_query().
        $host_id_results = \Drupal::database()->query('SELECT `entity_id` FROM {' . $table . '} ' . 'WHERE `' . $this->bundle() . '_value` = ' . $this->id())->fetchCol();
        $this->host_id = reset($host_id_results);
      }
      else {
        $this->host_id = NULL;
      }
    }

    return $this->host_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setHostEntity($entity, $create_link = TRUE) {
    if ($this->isNew()) {
      $this->host_type = $entity->getEntityTypeId();
      $this->host_id = $entity->id();
      $this->host_entity = $entity;

      // If the host entity is not saved yet, set the id to FALSE. So
      // fetchHostDetails() does not try to load the host entity details.
      if (!isset($this->host_id)) {
        $this->host_id = FALSE;
      }

      /*
      // We are create a new field collection for a non-default entity, thus
      // set archived to TRUE.
      if (!entity_revision_is_default($entity_type, $entity)) {
        $this->hostEntityId = FALSE;
        $this->archived = TRUE;
      }
      */

      // Add the field collection item to its host.
      if ($create_link) {
        if (_field_collection_field_item_list_full($entity->{$this->bundle()})) {
          drupal_set_message(t('Field is already full.'), 'error');
        }
        else {
          $entity->{$this->bundle()}[] = array('field_collection_item' => $this);
          $entity->save();
        }
      }
    }
    else {
      throw new \Exception(t('The host entity may be set only during creation of a field collection item.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $is_empty = TRUE;

    foreach ($this->getIterator() as $field) {
      // Only check configured fields, skip base fields like uuid.
      if (!$field->isEmpty() && 'Drupal\\field\\Entity\\FieldConfig' == get_class($field->getFieldDefinition())) {
        $is_empty = FALSE;
      }
    }

    // TODO: Allow other modules a chance to alter the value before returning?
    //drupal_alter('field_collection_is_empty', $is_empty, $this);

    return $is_empty;
  }

}
