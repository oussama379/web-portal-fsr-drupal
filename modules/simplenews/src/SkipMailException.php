<?php

namespace Drupal\simplenews;

/**
 * Exception to throw when a mail should not be sent.
 *
 * The Drupal mail system offers the $message['send'] flag which can be set to
 * FALSE to mark that a message should not be sent. This is however typically
 * considered a failure, and Simplenews handles it as an error (see
 * \Drupal\simplenews\Mail\Mailer).
 *
 * This exception can on the other hand be thrown in order to skip sending a
 * message and have it considered as an expected result. It should only be
 * thrown within the Drupal mail flow, for example within hook_mail() or
 * hook_mail_alter().
 */
class SkipMailException extends \RuntimeException {

}
