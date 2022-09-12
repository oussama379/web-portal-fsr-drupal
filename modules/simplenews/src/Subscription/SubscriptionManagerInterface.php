<?php

namespace Drupal\simplenews\Subscription;

use Drupal\simplenews\SubscriberInterface;

/**
 * Subscription management; subscribe, unsubscribe and get subscription status.
 */
interface SubscriptionManagerInterface {

  /**
   * Subscribe a user to a newsletter or send a confirmation mail.
   *
   * The $confirm parameter determines the action:
   *   FALSE = The user is subscribed
   *   TRUE  = User receives an email to verify the address and complete the
   * subscription A new subscription account is created when the user is
   * subscribed to the first newsletter.
   *
   * @param string $mail
   *   The email address to subscribe to the newsletter.
   * @param string $newsletter_id
   *   The newsletter ID.
   * @param bool|null $confirm
   *   TRUE = send confirmation mail; FALSE = subscribe immediate to the
   *   newsletter; NULL means the default from the chosen newsletter is used.
   * @param string $source
   *   Indication for source of subscription. Simplenews uses these sources:
   *    website: via any website form (with or without confirmation email)
   *    mass subscribe: mass admin UI
   *    mass unsubscribe: mass admin UI
   *    action: Drupal actions
   * @param string $preferred_langcode
   *   The language code (i.e. 'en', 'nl') of the user preferred language.
   *   Use '' for the site default language.
   *   Use NULL for the language of the current page.
   *
   * @return $this
   */
  public function subscribe($mail, $newsletter_id, $confirm = NULL, $source = 'unknown', $preferred_langcode = NULL);

  /**
   * Unsubscribe a user from a mailing list or send a confirmation mail.
   *
   * The $confirm parameter determines the action:
   *   FALSE = The user is unsubscribed
   *   TRUE  = User receives an email to verify the address and complete the
   *   subscription cancellation.
   *
   * @param string $mail
   *   The email address to unsubscribe from the mailing list.
   * @param string $newsletter_id
   *   The newsletter ID.
   * @param bool|null $confirm
   *   If TRUE, send a confirmation mail; if FALSE, unsubscribe immediately.
   *   NULL means the default from the chosen newsletter is used.
   * @param string $source
   *   Indicates the unsubscribe source. Simplenews uses these sources:
   *   - website: Via any website form (with or without confirmation email).
   *   - mass subscribe: Mass admin UI.
   *   - mass unsubscribe: Mass admin UI.
   *   - action: Drupal actions.
   *
   * @return $this
   */
  public function unsubscribe($mail, $newsletter_id, $confirm = NULL, $source = 'unknown');

  /**
   * Check if the email address is subscribed to the given mailing list.
   *
   * @param string $mail
   *   The email address to be checked.
   * @param string $newsletter_id
   *   The mailing list id.
   *
   * @return bool
   *   TRUE if the email address is subscribed; otherwise false.
   *
   * @ingroup subscription
   *
   * @todo Caching should be done in simplenews_load_user_by_mail().
   */
  public function isSubscribed($mail, $newsletter_id);

  /**
   * Converts an array of subscription changes into descriptions.
   *
   * @param \Drupal\simplenews\SubscriberInterface $subscriber
   *   Simplenews subscriber object.
   * @param array $changes
   *   (Optional) Array of changes, each is an array with the keys action and
   *   newsletter_id. Defaults to $subscriber->getChanges(), which contains the
   *   currently saved changes for the subscriber.
   * @param string $langcode
   *   (Optional) Specify the language of the description strings, defaults to
   *   the current language.
   *
   * @return string[]
   *   Array of description strings describing the changes.
   */
  public function getChangesList(SubscriberInterface $subscriber, $changes = NULL, $langcode = NULL);

  /**
   * Send confirmations for previous subscriptions and usubscription requests.
   *
   * @return int
   *   The amount of confirmation that were sent.
   */
  public function sendConfirmations();

  /**
   * Reset static caches.
   */
  public function reset();

}
