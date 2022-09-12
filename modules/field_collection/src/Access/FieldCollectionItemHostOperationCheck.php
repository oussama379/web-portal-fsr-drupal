<?php

/**
 * @file
 * Contains \Drupal\field_collection\Access\FieldCollectionItemHostOperationCheck.
 */

namespace Drupal\field_collection\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Drupal\field_collection\Entity\FieldCollectionItem;

/**
 * Determines access to operations on the field collection item's host.
 */
class FieldCollectionItemHostOperationCheck implements AccessInterface {

  /**
   * Checks access to the operation on the field collection item's host.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * TODO: Document params
   *
   * @return string
   *   A \Drupal\Core\Access\AccessInterface constant value.
   */
  public function access(Route $route, AccountInterface $account, $field_collection_item_revision = NULL, FieldCollectionItem $field_collection_item = NULL) {
    $operation = $route->getRequirement('_access_field_collection_item_host');

    return AccessResult::allowedIf($field_collection_item && $field_collection_item->getHost()->access($operation, $account))->cachePerPermissions();
  }

}
