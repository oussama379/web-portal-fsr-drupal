<?php

namespace Drupal\simplenews\Mail;

/**
 * Source cache implementation that caches build and data element.
 *
 * @ingroup mail
 */
class MailCacheBuild extends MailCacheStatic {

  /**
   * {@inheritdoc}
   */
  function isCacheable(MailInterface $mail, $group, $key) {

    // Only cache for anon users.
    if (\Drupal::currentUser()->isAuthenticated()) {
      return FALSE;
    }

    // Only cache data and build information.
    return in_array($group, array('data', 'build'));
  }

}
