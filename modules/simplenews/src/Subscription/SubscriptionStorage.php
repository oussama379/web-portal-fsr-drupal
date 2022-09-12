<?php

namespace Drupal\simplenews\Subscription;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\simplenews\SubscriberInterface;

/**
 * Default subscription storage.
 */
class SubscriptionStorage extends SqlContentEntityStorage implements SubscriptionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function deleteSubscriptions($conditions = array()) {
    $table_name = 'simplenews_subscriber__subscriptions';
    if (!db_table_exists($table_name)) {
      // This can happen if this is called during uninstall.
      return;
    }
    $query = $this->database->delete($table_name);
    foreach ($conditions as $key => $condition) {
      $query->condition($key, $condition);
    }
    $query->execute();
    $this->resetCache();
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscriptionsByNewsletter($newsletter_id) {
    $query = $this->database->select('simplenews_subscriber', 'sn');
    $query->innerJoin('simplenews_subscriber__subscriptions', 'ss', 'ss.entity_id = sn.id');
    $query->fields('sn', array('mail', 'uid', 'langcode', 'id'))
      ->fields('ss', array('subscriptions_status'))
      ->condition('sn.status', SubscriberInterface::ACTIVE)
      ->condition('subscriptions_target_id', $newsletter_id)
      ->condition('ss.subscriptions_status', SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED);
    return $query->execute()->fetchAllAssoc('mail');
  }
}
