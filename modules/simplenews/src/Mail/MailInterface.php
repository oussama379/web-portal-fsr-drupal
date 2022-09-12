<?php

namespace Drupal\simplenews\Mail;

/**
 * A newsletter mail.
 *
 * @ingroup mail
 */
interface MailInterface {

  /**
   * Returns the used entity for this mail.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   */
  function getEntity();

  /**
   * Returns the subscriber object.
   *
   * @return \Drupal\simplenews\SubscriberInterface
   */
  function getSubscriber();

  /**
   * Returns the mail headers.
   *
   * @param $headers
   *   The default mail headers.
   *
   * @return
   *   Mail headers as an array.
   */
  function getHeaders(array $headers);

  /**
   * Returns the mail subject.
   *
   * @return string
   *   The mail subject.
   */
  function getSubject();

  /**
   * Returns the mail body.
   *
   * @return string
   *   The body, as plaintext or html depending on the format.
   */
  function getBody();

  /**
   * Returns the plaintext body.
   *
   * @return string
   *   The body as plain text.
   */
  function getPlainBody();

  /**
   * Returns the mail format.
   *
   * @return string
   *   The mail format as string, either 'plain' or 'html'.
   */
  function getFormat();

  /**
   * Returns the recipient of this newsletter mail.
   *
   * @return string
   *   The recipient mail address(es) of this newsletter as a string.
   */
  function getRecipient();

  /**
   * The language that should be used for this newsletter mail.
   *
   * @return string
   *   The langcode.
   */
  function getLanguage();

  /**
   * Returns an array of attachments for this newsletter mail.
   *
   * @return array
   *   An array of managed file objects with properties uri, filemime and so on.
   */
  function getAttachments();

  /**
   * Returns the token context to be used with token replacements.
   *
   * @return array
   *   An array of objects as required by token_replace().
   */
  function getTokenContext();

  /**
   * Returns the mail key to be used for mails.
   *
   * @return string
   *   The mail key, either test or node.
   */
  function getKey();

  /**
   * Set the mail key.
   *
   * @param string $key
   *   The mail key, either 'test' or 'node'.
   */
  function setKey($key);

  /**
   * Returns the formatted from mail address.
   *
   * @return string
   *   The mail address with a name.
   */
  function getFromFormatted();

  /**
   * Returns the plain mail address.
   *
   * @return string
   *   The mail address.
   */
  function getFromAddress();
}
