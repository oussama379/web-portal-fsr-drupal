<?php

/**
 * @file
 * Contains \Drupal\field_collection\FieldCollectionItemInterface.
 */

namespace Drupal\field_collection;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a field collection item entity.
 */
interface FieldCollectionItemInterface extends ContentEntityInterface {

  /**
   * Returns the $delta of the reference to this field collection item.
   *
   * @return int|null
   *   The $delta of the reference to this field collection item, or NULL if
   *   the reference doesn't exist in the host yet.
   */
  public function getDelta();

  /**
   * Returns the host entity of this field collection item.
   *
   * @param bool
   *   (optional) TRUE to reset the internal cache for the host's entity type.
   *   Defaults to FALSE.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   The host entity of this field collection item or NULL if the reference
   *   doesn't exist in the host yet.
   */
  public function getHost($reset = FALSE);

  /**
   * Returns the id of the host entity for this field collection item.
   *
   * @return string|int|null
   *   The id of the host entity for this field collection item, or NULL if the
   *   reference doesn't exist in the host yet.
   */
  public function getHostId();

  /**
   * Sets the host entity. Only possible during creation of a item.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The host entity to add the the field collection item to.
   *
   * @param $create_link
   *   (optional) Whether a field-item linking the host entity to the field
   *   collection item should be created.  Defaults to TRUE.
   */
  public function setHostEntity($entity, $create_link = TRUE);

  /**
   * Determine whether a field collection item entity is empty.
   *
   * Checks individual collection-fields.
   *
   * @return bool
   *   TRUE if the field collection item is empty.
   */
  public function isEmpty();

}
