<?php

/**
 * @file
 * Contains \Drupal\field_collection\Access\FieldCollectionItemHostRevisionsOperationCheck.
 */

namespace Drupal\field_collection\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Drupal\field_collection\Entity\FieldCollectionItem;
use Drupal\user\PermissionHandlerInterface;

/**
 * Determines access to revision operations on the field collection item's host.
 */
class FieldCollectionItemHostRevisionsOperationCheck implements AccessInterface {

  /**
   * The permission handler.
   *
   * @var \Drupal\user\PermissionHandlerInterface
   */
  protected $permissionHandler;

  /**
   * The field collection item storage.
   *
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $fieldCollectionItemStorage;

  /**
   * Constructs a new FieldCollectionItemHostRevisionsOperationCheck.
   *
   * @param \Drupal\user\PermissionHandlerInterface $permission_handler
   *   The permission handler.
   */
  public function __construct(PermissionHandlerInterface $permission_handler) {
    $this->permissionHandler = $permission_handler;
  }

  /**
   * Checks operation access on the field collection item's host's revisions.
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
    if ($field_collection_item_revision) {
      $field_collection_item = \Drupal::entityTypeManager()->getStorage('field_collection_item')->loadRevision($field_collection_item_revision);
    }
    $operation = $route->getRequirement('_access_field_collection_item_host_revisions');

    $host = $field_collection_item->getHost();

    if ($host->getEntityType()->id() == 'node') {
      return AccessResult::allowedIf($account->hasPermission($operation . ' ' . $host->getType() . ' revisions'));
    }
    else if ($host->getEntityType()->id() == 'field_collection_item') {
      return $this->access($route, $account, $host->revision_id, $host);
    }
    // TODO: Other revisionable entity types?
    else {
      return AccessResult::allowedIf($field_collection_item && $field_collection_item->getHost()->access($operation, $account))->cachePerPermissions();
    }
  }

}
