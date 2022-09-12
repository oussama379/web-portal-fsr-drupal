<?php

namespace Drupal\simplenews\Subscription;

/**
 * Subscription storage.
 */
interface SubscriptionStorageInterface {

  /**
   * Deletes subscriptions.
   *
   * @param array $conditions
   *   An associative array of conditions matching the records to be delete.
   *   Example: array('newsletter_id' => 5, 'snid' => 12)
   *   Delete the subscription of subscriber 12 to newsletter newsletter_id 5.
   *
   * @ingroup subscription
   */
  public function deleteSubscriptions($conditions = array());

  /**
   * Returns a list of active subscriptions for a given newsletter.
   *
   * WARNING: Use with caution - this might return a huge list.
   *
   * @param string $newsletter_id
   *   The newsletter id.
   *
   * @return array
   *   An array keyed by the mail address, containing another array with the
   *   keys mail, uid, language, snid and status.
   *
   * @ingroup subscription
   */
  public function getSubscriptionsByNewsletter($newsletter_id);
}
