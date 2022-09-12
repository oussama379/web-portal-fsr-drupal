<?php

namespace Drupal\simplenews\Subscription;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DestructableInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Utility\Token;
use Drupal\simplenews\Entity\Subscriber;
use Drupal\simplenews\Mail\MailerInterface;
use Drupal\simplenews\NewsletterInterface;
use Drupal\simplenews\SubscriberInterface;

/**
 * Default subscription manager.
 */
class SubscriptionManager implements SubscriptionManagerInterface, DestructableInterface {

  /**
   * Whether confirmations should be combined.
   *
   * @var bool
   */
  protected $combineConfirmations = FALSE;

  /**
   * Combined confirmations.
   *
   * @var array
   */
  protected $confirmations = [];

  /**
   * @var array
   */
  protected $subscribedCache = [];

  /**
   * @var \Drupal\simplenews\Mail\MailerInterface
   */
  protected $mailer;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a SubscriptionManager.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\simplenews\Mail\MailerInterface $mailer
   *   The simplenews manager.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The simplenews logger channel.
   */
  public function __construct(LanguageManagerInterface $language_manager, ConfigFactoryInterface $config_factory, MailerInterface $mailer, Token $token, LoggerChannelInterface $logger, AccountInterface $current_user) {
    $this->languageManager = $language_manager;
    $this->config = $config_factory->get('simplenews.settings');
    $this->mailer = $mailer;
    $this->token = $token;
    $this->logger = $logger;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function subscribe($mail, $newsletter_id, $confirm = NULL, $source = 'unknown', $preferred_langcode = NULL) {
    // Get current subscriptions if any.
    $subscriber = simplenews_subscriber_load_by_mail($mail);

    // If user is not subscribed to ANY newsletter, create a subscription account
    if (!$subscriber) {
      // To subscribe a user:
      //   - Fetch the users uid.
      //   - Determine the user preferred language.
      //   - Add the user to the database.
      //   - Get the full subscription object based on the mail address.
      // Note that step 3 gets subscription data based on mail address because the uid can be 0 (for anonymous users)
      $account = user_load_by_mail($mail);

      // If the site is multilingual:
      //  - Anonymous users are subscribed with their preferred language
      //    equal to the language of the current page.
      //  - Registered users will be subscribed with their default language as
      //    set in their account settings.
      // By default the preferred language is not set.
      if ($this->languageManager->isMultilingual()) {
        if ($account) {
          $preferred_langcode = $account->getPreferredLangcode();
        }
        else {
          $preferred_langcode = isset($preferred_langcode) ? $preferred_langcode : $this->languageManager->getCurrentLanguage();
        }
      }
      else {
        $preferred_langcode = '';
      }

      $subscriber = Subscriber::create(array());
      $subscriber->setMail($mail);
      if ($account) {
        $subscriber->setUserId($account->id());
      }
      $subscriber->setLangcode($preferred_langcode);
      $subscriber->setStatus(SubscriberInterface::ACTIVE);
      $subscriber->save();
    }

    $newsletter = simplenews_newsletter_load($newsletter_id);

    // If confirmation is not explicitly specified, use the newsletter
    // configuration.
    if ($confirm === NULL) {
      $confirm = $this->requiresConfirmation($newsletter, $subscriber->getUserId());
    }

    if ($confirm) {
      // Create an unconfirmed subscription object if it doesn't exist yet.
      if (!$subscriber->isSubscribed($newsletter_id)) {
        $subscriber->subscribe($newsletter_id, SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED, $source);
        $subscriber->save();
      }

      $this->addConfirmation('subscribe', $subscriber, $newsletter);
    }
    elseif (!$subscriber->isSubscribed($newsletter_id)) {
      // Subscribe the user if not already subscribed.
      $subscriber->subscribe($newsletter_id, SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, $source);
      $subscriber->save();
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function unsubscribe($mail, $newsletter_id, $confirm = NULL, $source = 'unknown') {
    $subscriber = simplenews_subscriber_load_by_mail($mail);

    // The unlikely case that a user is unsubscribed from a non existing mailing list is logged
    if (!$newsletter = simplenews_newsletter_load($newsletter_id)) {
      $this->logger->error('Attempt to unsubscribe from non existing mailing list ID %id', array('%id' => $newsletter_id));
      return $this;
    }

    // If confirmation is not explicitly specified, use the newsletter
    // configuration.
    if ($confirm === NULL) {
      $confirm = $this->requiresConfirmation($newsletter, $subscriber->getUserId());
    }

    if ($confirm) {
      // Make sure the mail address is set.
      if (empty($subscriber)) {
        $subscriber = Subscriber::create(array());
        $subscriber->setMail($mail);
        $subscriber->save();
      }
      $this->addConfirmation('unsubscribe', $subscriber, $newsletter);
    }
    elseif ($subscriber && $subscriber->isSubscribed($newsletter_id)) {
      // Unsubscribe the user from the mailing list.
      $subscriber->unsubscribe($newsletter_id, $source);
      $subscriber->save();

    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isSubscribed($mail, $newsletter_id) {
    if (!isset($this->subscribedCache[$mail][$newsletter_id])) {
      $subscriber = simplenews_subscriber_load_by_mail($mail);
      // Check that a subscriber was found, he is active and subscribed to the
      // requested newsletter_id.
      $this->subscribedCache[$mail][$newsletter_id] = $subscriber && $subscriber->getStatus() && $subscriber->isSubscribed($newsletter_id);
    }
    return $this->subscribedCache[$mail][$newsletter_id];
  }

  /**
   * {@inheritdoc}
   */
  public function getChangesList(SubscriberInterface $subscriber, $changes = NULL, $langcode = NULL) {
    if (empty($langcode)) {
      $language = $this->languageManager->getCurrentLanguage();
      $langcode = $language->getId();
    }

    if (empty($changes)) {
      $changes = $subscriber->getChanges();
    }

    $changes_list = array();
    foreach ($changes as $newsletter_id => $action) {
      $subscribed = $subscriber->isSubscribed($newsletter_id);
      // Get text for each possible combination.
      if ($action == 'subscribe' && !$subscribed) {
        $line = $this->config->get('subscription.confirm_combined_line_subscribe_unsubscribed');
      }
      elseif ($action == 'subscribe' && $subscribed) {
        $line = $this->config->get('subscription.confirm_combined_line_subscribe_subscribed');
      }
      elseif ($action == 'unsubscribe' && !$subscribed) {
        $line = $this->config->get('subscription.confirm_combined_line_unsubscribe_unsubscribed');
      }
      elseif ($action == 'unsubscribe' && $subscribed) {
        $line = $this->config->get('subscription.confirm_combined_line_unsubscribe_subscribed');
      }
      $newsletter_context = array(
        'simplenews_subscriber' => $subscriber,
        'newsletter' => simplenews_newsletter_load($newsletter_id),
      );
      $changes_list[$newsletter_id] = $this->token->replace($line, $newsletter_context, array('sanitize' => FALSE));
    }
    return $changes_list;
  }

  /**
   * {@inheritdoc}
   */
  public function sendConfirmations() {
    foreach ($this->confirmations as $mail => $changes) {
      $subscriber = simplenews_subscriber_load_by_mail($mail);
      if (!$subscriber) {
        $subscriber = Subscriber::create(array());
        $subscriber->setMail($mail);
        $subscriber->setLangcode($this->languageManager->getCurrentLanguage());
        $subscriber->save();
      }
      $subscriber->setChanges($changes);

      $this->mailer->sendCombinedConfirmation($subscriber);

      // Save the changes in the subscriber if there is a real subscriber object.
      if ($subscriber && $subscriber->id()) {
        $subscriber->save();
      }
    }
    $sent = !empty($this->confirmations);
    $this->confirmations = array();
    return $sent;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->subscribedCache = [];
  }

  /**
   * {@inheritdoc}
   */
  public function destruct() {
    // Ensure that confirmations are always sent even if API calls did not do it
    // explicitly. It is still possible to do so, e.g. to be able to know if
    // confirmations were sent or not.
    $this->sendConfirmations();
  }

  /**
   * Add a mail confirmation or fetch them.
   *
   * @param string $action
   *   The confirmation type, either subscribe or unsubscribe.
   * @param \Drupal\simplenews\SubscriberInterface $subscriber
   *   The subscriber object.
   * @param \Drupal\simplenews\NewsletterInterface $newsletter
   *   The newsletter object.
   */
  protected function addConfirmation($action, SubscriberInterface $subscriber, NewsletterInterface $newsletter) {
    $this->confirmations[$subscriber->getMail()][$newsletter->id()] = $action;
  }

  /**
   * Checks whether confirmation is required for this newsletter and user.
   *
   * @param \Drupal\simplenews\NewsletterInterface $newsletter
   *   The newsletter entity.
   * @param int $uid
   *   The user ID that belongs to the email.
   *
   * @return bool
   *   TRUE if confirmation is required, FALSE if not.
   */
  protected function requiresConfirmation(NewsletterInterface $newsletter, $uid) {
    // If user is currently logged in, don't send confirmation.
    // Other addresses receive a confirmation if double opt-in is selected.
    if ($this->currentUser->id() && $uid && $this->currentUser->id() == $uid) {
      return FALSE;
    }
    else {
      return $newsletter->opt_inout == 'double';
    }
  }


}
