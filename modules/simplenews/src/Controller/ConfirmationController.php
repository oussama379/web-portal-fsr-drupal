<?php

namespace Drupal\simplenews\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for confirmation routes.
 */
class ConfirmationController extends ControllerBase {

  /**
   * Menu callback: confirm a combined confirmation request.
   *
   * This function is called by clicking the confirm link in the confirmation
   * email. It handles both subscription addition and subscription removal.
   *
   * @see simplenews_confirm_add_form()
   * @see simplenews_confirm_removal_form()
   *
   * @param $snid
   *   The subscriber id.
   * @param $timestamp
   *   The timestamp of the request.
   * @param $hash
   *   The confirmation hash.
   */
  public function confirmCombined($snid, $timestamp, $hash, $immediate = FALSE) {
    $config = \Drupal::config('simplenews.settings');

    // Prevent search engines from indexing this page.
    $html_head = array(
      array(
        '#tag' => 'meta',
        '#attributes' => array(
          'name' => 'robots',
          'content' => 'noindex',
        ),
      ),
      'simplenews-noindex',
    );

    $subscriber = simplenews_subscriber_load($snid);

    // Redirect and display message if no changes are available.
    if ($subscriber && !$subscriber->getChanges()) {
      drupal_set_message(t('All changes to your subscriptions where already applied. No changes made.'));
      return $this->redirect('<front>');
    }

    if ($subscriber && $hash == simplenews_generate_hash($subscriber->getMail(), 'combined' . serialize($subscriber->getChanges()), $timestamp)) {
      // If the hash is valid but timestamp is too old, display form to request
      // a new hash.
      if ($timestamp < REQUEST_TIME - $config->get('hash_expiration')) {
        $context = array(
          'simplenews_subscriber' => $subscriber,
        );
        $build = \Drupal::formBuilder()->getForm('\Drupal\simplenews\Form\RequestHashForm', 'subscribe_combined', $context);
        $build['#attached']['html_head'][] = $html_head;
        return $build;
      }
      // When not called with immediate parameter the user will be directed to the
      // (un)subscribe confirmation page.
      if (!$immediate) {
        $build = \Drupal::formBuilder()->getForm('\Drupal\simplenews\Form\ConfirmMultiForm', $subscriber);
        $build['#attached']['html_head'][] = $html_head;
        return $build;
      }
      else {

        /** @var \Drupal\simplenews\Subscription\SubscriptionManagerInterface $subscription_manager */
        $subscription_manager = \Drupal::service('simplenews.subscription_manager');

        // Redirect and display message if no changes are available.
        foreach ($subscriber->getChanges() as $newsletter_id => $action) {
          if ($action == 'subscribe') {
            $subscription_manager->subscribe($subscriber->getMail(), $newsletter_id, FALSE, 'website');
          }
          elseif ($action == 'unsubscribe') {
            $subscription_manager->unsubscribe($subscriber->getMail(), $newsletter_id, FALSE, 'website');
          }
        }

        // Clear changes.
        $subscriber->setChanges(array());
        $subscriber->save();

        drupal_set_message(t('Subscription changes confirmed for %user.', array('%user' => $subscriber->getMail())));
        return $this->redirect('<front>');
      }
    }
    throw new NotFoundHttpException();
  }

  /**
   * Menu callback: confirm the user's (un)subscription request
   *
   * This function is called by clicking the confirm link in the confirmation
   * email or the unsubscribe link in the footer of the newsletter. It handles
   * both subscription addition and subscription removal.
   *
   * Calling URLs are:
   * newsletter/confirm/add
   * newsletter/confirm/add/$HASH
   * newsletter/confirm/remove
   * newsletter/confirm/remove/$HASH
   *
   * @see simplenews_confirm_add_form()
   * @see simplenews_confirm_removal_form()
   */

  /**
   * Menu callback: confirm the user's (un)subscription request
   *
   * This function is called by clicking the confirm link in the confirmation
   * email or the unsubscribe link in the footer of the newsletter. It handles
   * both subscription addition and subscription removal.
   *
   * @see simplenews_confirm_add_form()
   * @see simplenews_confirm_removal_form()
   *
   * @param $action
   *   Either add or remove.
   * @param $snid
   *   The subscriber id.
   * @param $newsletter_id
   *   The newsletter id.
   * @param $timestamp
   *   The timestamp of the request.
   * @param $hash
   *   The confirmation hash.
   */
  function confirmSubscription($action, $snid, $newsletter_id, $timestamp, $hash, $immediate = FALSE) {
    $config = \Drupal::config('simplenews.settings');

    // Prevent search engines from indexing this page.
    $html_head = array(
      array(
        '#tag' => 'meta',
        '#attributes' => array(
          'name' => 'robots',
          'content' => 'noindex',
        ),
      ),
      'simplenews-noindex',
    );

    $subscriber = simplenews_subscriber_load($snid);
    if ($subscriber && $hash == simplenews_generate_hash($subscriber->getMail(), $action, $timestamp)) {
      $newsletter = simplenews_newsletter_load($newsletter_id);

      // If the hash is valid but timestamp is too old, display form to request
      // a new hash.
      if ($timestamp < REQUEST_TIME - $config->get('hash_expiration')) {
        $context = array(
          'simplenews_subscriber' => $subscriber,
          'newsletter' => $newsletter,
        );
        $token = $action == 'add' ? 'subscribe' : 'unsubscribe';
        $build = \Drupal::formBuilder()->getForm('\Drupal\simplenews\Form\RequestHashForm', $token, $context);
        $build['#attached']['html_head'][] = $html_head;
        return $build;
      }
      // When called with additional arguments the user will be directed to the
      // (un)subscribe confirmation page. The additional arguments will be passed
      // on to the confirmation page.
      if (!$immediate) {
        if ($action == 'remove') {
          $build = \Drupal::formBuilder()->getForm('\Drupal\simplenews\Form\ConfirmRemovalForm', $subscriber->getMail(), $newsletter);
          $build['#attached']['html_head'][] = $html_head;
          return $build;
        }
        elseif ($action == 'add') {
          $build = \Drupal::formBuilder()->getForm('\Drupal\simplenews\Form\ConfirmAddForm', $subscriber->getMail(), $newsletter);
          $build['#attached']['html_head'][] = $html_head;
          return $build;
        }
      }
      else {

        /** @var \Drupal\simplenews\Subscription\SubscriptionManagerInterface $subscription_manager */
        $subscription_manager = \Drupal::service('simplenews.subscription_manager');

        if ($action == 'remove') {
          $subscription_manager->unsubscribe($subscriber->getMail(), $newsletter_id, FALSE, 'website');
          if ($path = $config->get('subscription.confirm_unsubscribe_page')) {
            return $this->redirect(Url::fromUri("internal:/$path")->getRouteName());
          }
          drupal_set_message(t('%user was unsubscribed from the %newsletter mailing list.', array('%user' => $subscriber->getMail(), '%newsletter' => $newsletter->name)));
          return $this->redirect('<front>');
        }
        elseif ($action == 'add') {
          $subscription_manager->subscribe($subscriber->getMail(), $newsletter_id, FALSE, 'website');
          if ($path = $config->get('subscription.confirm_subscribe_page')) {
            return $this->redirect(Url::fromUri("internal:/$path")->getRouteName());
          }
          drupal_set_message(t('%user was added to the %newsletter mailing list.', array('%user' => $subscriber->getMail(), '%newsletter' => $newsletter->name)));
          return $this->redirect('<front>');
        }
      }
    }
    throw new NotFoundHttpException();
  }
}
