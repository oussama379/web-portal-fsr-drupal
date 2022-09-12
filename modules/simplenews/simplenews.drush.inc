<?php
/**
 * @file
 *
 * Drush commands for administer Simplenews.
 */
use Drupal\simplenews\Mail\MailerInterface;
use Drupal\simplenews\Spool\SpoolStorageInterface;

/**
 * Implements hook_drush_command().
 */
function simplenews_drush_command() {
  $items = array();

  $items['simplenews-spool-count'] = array(
    'description' => 'Print the current simplenews mail spool count',
    'aliases' => array('sn-sc'),
    'drupal dependencies' => array('simplenews'),
    'options' => array(
      'pipe' => dt('Just print the count value to allow parsing'),
    )
  );

  $items['simplenews-spool-send'] = array(
    'description' => 'Send the defined amount of mail spool entries.',
    'examples' => array(
      'drush sn-ss' => dt('Send the default amount of mails, as defined by the simplenews_throttle variable.'),
      'drush sn-ss 0' => dt('Send all mails.'),
      'drush sn-ss 100' => dt('Send 100 mails'),
    ),
    'options' => array(
      'pipe' => dt('Just print the sent and remaining count on separate lines to allow parsing'),
    ),
    'aliases' => array('sn-ss'),
    'drupal dependencies' => array('simplenews'),
  );

  return $items;
}

/**
 * Drush command to count the mail spool queue.
 */
function drush_simplenews_spool_count() {

  $count = \Drupal::service('simplenews.spool_storage')->countMails();

  $no_description = drush_get_option(array('p', 'pipe'));
  if ($no_description) {
    drush_print_pipe($count);
  }
  else {
    drush_log(dt('Current simplenews mail spool count: @count', array('@count' => $count)), 'status');
  }
}

/**
 * Drush command to send the mail spool queue.
 */
function drush_simplenews_spool_send($limit = FALSE) {
  if (!simplenews_assert_uri()) {
    drush_set_error('Site URI not specified, use --uri.');
    return;
  }

  if ($limit === FALSE) {
    $limit = variable_get('simplenews_throttle');
  }
  elseif ($limit == 0) {
    $limit = SpoolStorageInterface::UNLIMITED;
  }

  $start_time = microtime(TRUE);

  $sent = \Drupal::service('simplenews.mailer')->sendSpool($limit);
  \Drupal::service('simplenews.spool_storage')->clear();
  \Drupal::service('simplenews.mailer')->updateSendStatus();

  $durance = round(microtime(TRUE) - $start_time, 2);

  // Report the number of sent mails.
  if ($sent > 0) {
    $remaining = \Drupal::service('simplenews.spool_storage')->countMesages();
    if (drush_get_option(array('p', 'pipe'))) {
      // For pipe, print the sent first and then the remaining count, separated by a space.
      drush_print_pipe($sent . " " . $remaining);
    }
    else {
      drush_log(dt('Sent @count mails from the queue in @sec seconds.', array('@count' => $sent, '@sec' => $durance)), 'status');
      drush_log(dt('Remaining simplenews mail spool count: @count', array('@count' => $remaining)), 'status');
    }
  }

}
