<?php

namespace Drupal\simplenews\Mail;

/**
 * Builds newsletter and confirmation mails.
 */
interface MailBuilderInterface {

  /**
   * Build subject and body of the test and normal newsletter email.
   *
   * @param array $message
   *   Message array as used by hook_mail().
   * @param \Drupal\simplenews\Mail\MailInterface $mail
   *   The mail object.
   */
  function buildNewsletterMail(array &$message, MailInterface $mail);

  /**
   * Build subject and body of the subscribe confirmation email.
   *
   * @param array $message
   *   Message array as used by hook_mail().
   * @param array $params
   *   Parameter array as used by hook_mail().
   */
  function buildSubscribeMail(array &$message, array $params);

  /**
   * Build subject and body of the subscribe confirmation email.
   *
   * @param array $message
   *   Message array as used by hook_mail().
   * @param array $params
   *   Parameter array as used by hook_mail().
   */
  function buildCombinedMail(&$message, $params);

  /**
   * Build subject and body of the unsubscribe confirmation email.
   *
   * @param array $message
   *   Message array as used by hook_mail().
   * @param array $params
   *   Parameter array as used by hook_mail().
   */
  function buildUnsubscribeMail(&$message, $params);
}
