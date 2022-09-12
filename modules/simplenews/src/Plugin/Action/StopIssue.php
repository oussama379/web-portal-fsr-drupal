<?php

namespace Drupal\simplenews\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Stops a newsletter issue.
 *
 * @Action(
 *   id = "simplenews_stop_action",
 *   label = @Translation("Stop sending"),
 *   type = "node"
 * )
 */
class StopIssue extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    foreach ($entities as $node) {
      if ($node->simplenews_issue->status == SIMPLENEWS_STATUS_SEND_PENDING) {
        // Delete the mail spool entries of this newsletter issue.
        $count = \Drupal::service('simplenews.spool_storage')->deleteMails(array('nid' => $node->id()));
        // Set newsletter issue to not sent yet.
        $node->simplenews_issue->status = SIMPLENEWS_STATUS_SEND_NOT;
        $node->save();
        drupal_set_message(t('Sending of %title was stopped. @count pending email(s) were deleted.', array(
          '%title' => $node->getTitle(),
          '@count' => $count,
        )));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function execute($node = NULL) {
    $this->executeMultiple(array($node));
  }

  /**
   * {@inheritdoc}
   */
  public function access($node, AccountInterface $account = NULL, $return_as_object = FALSE) {

    if ($node->hasField('simplenews_issue') && $node->simplenews_issue->target_id != NULL) {
      return AccessResult::allowedIfHasPermission($account, 'administer newsletters')
        ->orIf(AccessResult::allowedIfHasPermission($account, 'send newsletter'));
    }
    return AccessResult::neutral();
  }
}
