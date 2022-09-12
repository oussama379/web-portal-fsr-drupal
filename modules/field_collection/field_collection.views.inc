<?php

/**
 * @file
 * Provide views data that isn't tied to any other module.
 */

use Drupal\field\FieldStorageConfigInterface;
use Drupal\views;

/**
 * Implements hook_field_views_data().
 *
 * Views integration for field collection fields. Adds a relationship to the
 * default field data.
 *
 * @see field_views_field_default_views_data()
 */
function field_collection_field_views_data(FieldStorageConfigInterface $field_storage) {
  $data = views_field_default_views_data($field_storage);
  $storage = _views_field_get_entity_type_storage($field_storage);
  /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $table_mapping */ 
  $table_mapping = $storage->getTableMapping();

  $entity_type_id = $field_storage->getTargetEntityTypeId();
  $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);

  $field_name = $field_storage->getName();

  foreach ($data as $table_name => $table_data) {
    foreach ($table_data as $table_field_name => $field_data) {
      // Only operate on the _value columns.
      if (strrpos($table_field_name, '_value') === (strlen($table_field_name) - strlen('_value'))) {
        $data[$table_name][$table_field_name]['relationship'] = array(
          'id' => 'field_collection_handler_relationship',
          'base' => 'field_collection_item',
          'base field' => 'item_id',
          'label' => t('field collection item from @field_name', array('@field_name' => $field_name)),
          'field name' => $field_name,
          'target entity type' => $entity_type_id,
        );
      }
    } 
  } 

  $args['@entity'] = $entity_type->getLabel()->render();
  $args['@entity'] = ($args['@entity'] == t('Content')) ? t('Node') : $args['@entity'];
  list($args['@field']) = views_entity_field_label($entity_type_id, $field_name);
  $args['@field_name'] = $field_name;
  $pseudo_field_name = $field_name . '_' . $entity_type_id;

  $data['field_collection_item'][$pseudo_field_name]['relationship'] = array(
    'title' => t('Host entity of @field (@field_name)', $args),
    'label' => t('@field_name', $args),
    'group' => $entity_type->getLabel(),
    'help' => t('Relate each @entity using @field.', $args),
    'id' => 'entity_reverse',
    'base' => $entity_type->getDataTable() ?: $entity_type->getBaseTable(),
    'base field' => $entity_type->getKey('id'),
    'field_name' => $field_name,
    'field table' => $table_mapping->getDedicatedDataTableName($field_storage),
    'field field' => $field_name . '_value',
  );
 

  return $data;
}
