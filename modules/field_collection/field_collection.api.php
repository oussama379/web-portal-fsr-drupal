<?php

/**
 * @file
 * Contains API documentation and examples for the Field collection module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter whether a field collection item is considered empty.
 *
 * This hook allows modules to determine whether a field collection is empty
 * before it is saved.
 *
 * @param boolean $is_empty
 *   Whether or not the field should be considered empty.
 * @param \Drupal\field_collection\Entity\FieldCollectionItem $item
 *   The field collection we are currently operating on.
 */
function hook_field_collection_is_empty_alter(&$is_empty, \Drupal\field_collection\Entity\FieldCollectionItem $item) {
  if (isset($item->my_field) && empty($item->my_field)) {
    $is_empty = TRUE;
  }
}

/**
 * @}
 */