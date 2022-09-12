<?php

namespace Drupal\simplenews\Mail;

/**
 * Abstract implementation of the mail caching that does static caching.
 *
 * Subclasses need to implement the abstract function isCacheable() to decide
 * what should be cached.
 *
 * @ingroup mail
 */
abstract class MailCacheStatic implements MailCacheInterface {

  /**
   * The static cache.
   */
  protected $cache = array();

  /**
   * Returns the cache identifier for the mail.
   *
   * @param \Drupal\simplenews\Mail\MailInterface $mail
   *   The mail object.
   *
   * @return string
   */
  protected function getCid(MailInterface $mail) {
    $entity_id = $mail->getEntity()->id();
    return $mail->getEntity()->getEntityTypeId() . ':' . $entity_id . ':' . $mail->getLanguage();
  }

  /**
   * {@inheritdoc}
   */
  public function get(MailInterface $mail, $group, $key) {
    if (!$this->isCacheable($mail, $group, $key)) {
      return NULL;
    }

    if (isset($this->cache[$this->getCid($mail)][$group][$key])) {
      return $this->cache[$this->getCid($mail)][$group][$key];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function set(MailInterface $mail, $group, $key, $data) {
    if (!$this->isCacheable($mail, $group, $key)) {
      return;
    }

    $this->cache[$this->getCid($mail)][$group][$key] = $data;
  }

  /**
   * Return if the requested element should be cached.
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
   * @return bool
   *   TRUE if it should be cached, FALSE otherwise.
   */
  abstract function isCacheable(MailInterface $mail, $group, $key);
}
