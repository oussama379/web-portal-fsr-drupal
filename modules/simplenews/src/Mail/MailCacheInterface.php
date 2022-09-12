<?php

namespace Drupal\simplenews\Mail;

/**
 * Interface for a simplenews mail cache implementation.
 *
 * @ingroup mail
 */
interface MailCacheInterface {

  /**
   * Return a cached element, if existing.
   *
   * Although group and key can be used to identify the requested cache, the
   * implementations are responsible to create a unique cache key themself using
   * the $mail. For example based on the node id and the language.
   *
   * @param \Drupal\simplenews\Mail\MailInterface $mail
   *   The mail object.
   * @param string $group
   *   Group of the cache key, which allows cache implementations to decide what
   *   they want to cache. Currently used groups:
   *     - data: Raw data, e.g. attachments.
   *     - build: Built and themed content, before personalizations like tokens.
   *     - final: The final returned data. Caching this means that newsletter
   *       can not be personalized anymore.
   * @param string $key
   *   Identifies the requested element, e.g. body or attachments.
   *
   * @return mixed
   *   The cached data or NULL.
   */
  function get(MailInterface $mail, $group, $key);

  /**
   * Write an element to the cache.
   *
   * Although group and key can be used to identify the requested cache, the
   * implementations are responsible to create a unique cache key themself using
   * the $mail. For example based on the entity id and the language.
   *
   * @param \Drupal\simplenews\Mail\MailInterface $mail
   *   The mail object.
   * @param string $group
   *   Group of the cache key, which allows cache implementations to decide what
   *   they want to cache. Currently used groups:
   *     - data: Raw data, e.g. attachments.
   *     - build: Built and themed content, before personalizations like tokens.
   *     - final: The final returned data. Caching this means that newsletter
   *       can not be personalized anymore.
   * @param string $key
   *   Identifies the requested element, e.g. body or attachments.
   * @param mixed $data
   *   The data to be saved in the cache.
   */
  function set(MailInterface $mail, $group, $key, $data);
}
