<?php

namespace Drupal\simplenews\Mail;

/**
 * Cache implementation that does not cache anything at all.
 *
 * @ingroup mail
 */
class MailCacheNone extends MailCacheStatic {

  /**
   * {@inheritdoc}
   */
  public function isCacheable(MailInterface $mail, $group, $key) {
    return FALSE;
  }

}
