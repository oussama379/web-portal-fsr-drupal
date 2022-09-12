<?php

/**
 * @file
 * Contains \Drupal\field_collection\Access\FieldCollectionItemHostAddOperationCheck.
 */

namespace Drupal\field_collection\Access;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Determines access to operations on the field collection item's host.
 */
class FieldCollectionItemHostAddOperationCheck implements AccessInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a FieldCollectionItemHostAddOperationCheck object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Checks access to add a field collection item to its future host.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * TODO: Document params
   *
   * @return string
   *   A \Drupal\Core\Access\AccessInterface constant value.
   */
  public function access(AccountInterface $account, $host_type, $host_id) {
    $access_control_handler = $this->entityTypeManager->getAccessControlHandler($host_type);

    $host = $this->entityTypeManager->getStorage($host_type)->load($host_id);

    return $access_control_handler->access($host, 'update', $account, TRUE);
  }

}
