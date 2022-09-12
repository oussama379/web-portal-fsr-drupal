<?php

namespace Drupal\simplenews\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Sends a newsletter issue.
 *
 * @Action(
 *   id = "simplenews_send_action",
 *   label = @Translation("Send newsletter issue"),
 *   type = "node"
 * )
 */
class SendIssue extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    $nodes = array();
    $labels = array();
    foreach ($entities as $node) {
      if ($node->simplenews_issue->status != SIMPLENEWS_STATUS_SEND_NOT) {
        continue;
      }
      if (!$node->isPublished()) {
        simplenews_issue_update_sent_status($node, SIMPLENEWS_COMMAND_SEND_PUBLISH);
        drupal_set_message(t('Newsletter issue %title is unpublished and will be sent on publish.', array('%title' => $node->label())));
        continue;
      }
      \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
      $nodes[$node->id()] = $node;
      $labels[$node->id()] = $node->label();
    }
    // If there were any newsletters sent, display a message.
    if (!empty($nodes)) {
      $conditions = array('entity_id' => array_keys($nodes), 'entity_type' => 'node');
      // Attempt to send immediatly, if configured to do so.
      if (\Drupal::service('simplenews.mailer')->attemptImmediateSend($conditions)) {
        drupal_set_message(t('Sent the following newsletter(s): %titles.', array('%titles' => implode(', ', $labels))));
        $status = SIMPLENEWS_STATUS_SEND_READY;
      }
      else {
        drupal_set_message(t('The following newsletter(s) are now pending: %titles.', array('%titles' => implode(', ', $labels))));
        $status = SIMPLENEWS_STATUS_SEND_PENDING;
      }
      foreach ($nodes as $node) {
        simplenews_issue_update_sent_status($node, $status);
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
