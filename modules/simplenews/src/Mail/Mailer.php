<?php

namespace Drupal\simplenews\Mail;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\simplenews\Entity\Subscriber;
use Drupal\simplenews\NewsletterInterface;
use Drupal\simplenews\Mail\MailEntity;
use Drupal\simplenews\Mail\MailInterface;
use Drupal\simplenews\SkipMailException;
use Drupal\simplenews\Spool\SpoolStorageInterface;
use Drupal\simplenews\SubscriberInterface;

/**
 * Default Mailer.
 */
class Mailer implements MailerInterface {

  /**
   * Amount of mails after which the execution time should be checked again.
   */
  const SEND_CHECK_INTERVAL = 100;

  /**
   * At 80% of the PHP max execution time, sending is interrupted.
   */
  const SEND_TIME_LIMIT = 0.8;

  /**
   * @var \Drupal\simplenews\Spool\SpoolStorageInterface
   */
  protected $spoolStorage;

  /**
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * @var \Drupal\Core\Session\AccountSwitcherInterface
   */
  protected $accountSwitcher;

  /**
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Start time of the timer.
   *
   * @var float
   */
  protected $startTime;

  /**
   * Constructs a Mailer.
   *
   * @param \Drupal\simplenews\Spool\SpoolStorageInterface $spool_storage
   *   The simplenews spool storage.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   Logger channel.
   * @param \Drupal\Core\Session\AccountSwitcherInterface $account_switcher
   *   Account switcher.
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   Lock service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(SpoolStorageInterface $spool_storage, MailManagerInterface $mail_manager, StateInterface $state, LoggerChannelInterface $logger, AccountSwitcherInterface $account_switcher, LockBackendInterface $lock, ConfigFactoryInterface $config_factory) {
    $this->spoolStorage = $spool_storage;
    $this->mailManager = $mail_manager;
    $this->state = $state;
    $this->logger = $logger;
    $this->accountSwitcher = $account_switcher;
    $this->lock = $lock;
    $this->config = $config_factory->get('simplenews.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function attemptImmediateSend(array $conditions = array(), $use_batch = TRUE) {
    if ($this->config->get('mail.use_cron')) {
      return FALSE;
    }
    if ($use_batch) {
      // Set up as many send operations as necessary to send all mails with the
      // defined throttle amount.
      $throttle = $this->config->get('mail.throttle');
      $spool_count = $this->spoolStorage->countMails($conditions);
      $num_operations = ceil($spool_count / $throttle);

      $operations = array();
      for ($i = 0; $i < $num_operations; $i++) {
        $operations[] = array('_simplenews_batch_dispatcher', array('simplenews.mailer:sendSpool', $throttle, $conditions));
      }

      // Add separate operations to clear the spool and update the send status.
      $operations[] = array('_simplenews_batch_dispatcher', array('simplenews.spool_storage:clear'));
      $operations[] = array('_simplenews_batch_dispatcher', array('simplenews.mailer:updateSendStatus'));

      $batch = array(
        'operations' => $operations,
        'title' => t('Sending mails'),
      );
      batch_set($batch);
    }
    else {
      // Send everything that matches the conditions immediately.
      $this->sendSpool(SpoolStorageInterface::UNLIMITED, $conditions);
      $this->spoolStorage->clear();
      $this->updateSendStatus();
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function sendSpool($limit = SpoolStorageInterface::UNLIMITED, array $conditions = array()) {
    $check_counter = 0;

    // Send pending messages from database cache.
    $spool = $this->spoolStorage->getMails($limit, $conditions);
    if (count($spool) > 0) {

      // Switch to the anonymous user.
      $anonymous_user = new AnonymousUserSession();
      $this->accountSwitcher->switchTo($anonymous_user);

      $count_fail = $count_skipped = $count_success = 0;
      $sent = array();

      $this->startTimer();

      while ($mail = $spool->nextMail()) {
        $mail->setKey('node');
        $result = $this->sendMail($mail);

        // Update spool status.
        // This is not optimal for performance but prevents duplicate emails
        // in case of PHP execution time overrun.
        foreach ($spool->getProcessed() as $msid => $row) {
          $row_result = isset($row->result) ? $row->result : $result;
          $this->spoolStorage->updateMails(array($msid), $row_result);
          if ($row_result['status'] == SpoolStorageInterface::STATUS_DONE) {
            $count_success++;
            if (!isset($sent[$row->entity_type][$row->entity_id][$row->langcode])) {
              $sent[$row->entity_type][$row->entity_id][$row->langcode] = 1;
            }
            else {
              $sent[$row->entity_type][$row->entity_id][$row->langcode]++;
            }
          }
          elseif ($row_result['status'] == SpoolStorageInterface::STATUS_SKIPPED) {
            $count_skipped++;
          }
          if ($row_result['error']) {
            $count_fail++;
          }
        }

        // Check every n emails if we exceed the limit.
        // When PHP maximum execution time is almost elapsed we interrupt
        // sending. The remainder will be sent during the next cron run.
        if (++$check_counter >= static::SEND_CHECK_INTERVAL && ini_get('max_execution_time') > 0) {
          $check_counter = 0;
          // Break the sending if a percentage of max execution time was exceeded.
          $elapsed = $this->getCurrentExecutionTime();
          if ($elapsed > static::SEND_TIME_LIMIT * ini_get('max_execution_time')) {
            $this->logger->warning('Sending interrupted: PHP maximum execution time almost exceeded. Remaining newsletters will be sent during the next cron run. If this warning occurs regularly you should reduce the !cron_throttle_setting.', array(
              '!cron_throttle_setting' => \Drupal::l(t('Cron throttle setting'), new Url('simplenews.settings_mail')),
            ));
            break;
          }
        }
      }

      // It is possible that all or at the end some results failed to get
      // prepared, report them separately.
      foreach ($spool->getProcessed() as $msid => $row) {
        $row_result = $row->result;
        $this->spoolStorage->updateMails(array($msid), $row_result);
        if ($row_result['status'] == SpoolStorageInterface::STATUS_DONE) {
          $count_success++;
          if (isset($row->langcode)) {
            if (!isset($sent[$row->entity_type][$row->entity_id][$row->langcode])) {
              $sent[$row->entity_type][$row->entity_id][$row->langcode] = 1;
            }
            else {
              $sent[$row->entity_type][$row->entity_id][$row->langcode]++;
            }
          }
        }
        elseif ($row_result['status'] == SpoolStorageInterface::STATUS_SKIPPED) {
          $count_skipped++;
        }
        if ($row_result['error']) {
          $count_fail++;
        }
      }

      // Update subscriber count.
      if ($this->lock->acquire('simplenews_update_sent_count')) {
        foreach ($sent as $entity_type => $ids) {
          foreach ($ids as $entity_id => $languages) {
            \Drupal::entityManager()->getStorage($entity_type)->resetCache(array($entity_id));
            $entity = entity_load($entity_type, $entity_id);
            foreach ($languages as $langcode => $count) {
              $translation = $entity->getTranslation($langcode);
              $translation->simplenews_issue->sent_count = $translation->simplenews_issue->sent_count + $count;
            }
            $entity->save();
          }
        }
        $this->lock->release('simplenews_update_sent_count');
      }

      // Report sent result and elapsed time. On Windows systems getrusage() is
      // not implemented and hence no elapsed time is available.
      if (function_exists('getrusage')) {
        $this->logger->notice('%success emails sent in %sec seconds, %skipped skipped, %fail failed sending.', array('%success' => $count_success, '%sec' => round($this->getCurrentExecutionTime(), 1), '%skipped' => $count_skipped, '%fail' => $count_fail));
      }
      else {
        $this->logger->notice('%success emails sent, %skipped skipped, %fail failed.', array('%success' => $count_success, '%skipped' => $count_skipped, '%fail' => $count_fail));
      }

      $this->state->set('simplenews.last_cron', REQUEST_TIME);
      $this->state->set('simplenews.last_sent', $count_success);

      $this->accountSwitcher->switchBack();
      return $count_success;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function sendMail(MailInterface $mail) {
    $params['simplenews_mail'] = $mail;

    // Send mail.
    try {
      $message = $this->mailManager->mail('simplenews', $mail->getKey(), $mail->getRecipient(), $mail->getLanguage(), $params, $mail->getFromFormatted());

      // Log sent result in watchdog.
      if ($this->config->get('mail.debug')) {
        if ($message['result']) {
          $this->logger->debug('Outgoing email. Message type: %type<br />Subject: %subject<br />Recipient: %to', array('%type' => $mail->getKey(), '%to' => $message['to'], '%subject' => $message['subject']));
        }
        else {
          $this->logger->error('Outgoing email failed. Message type: %type<br />Subject: %subject<br />Recipient: %to', array('%type' => $mail->getKey(), '%to' => $message['to'], '%subject' => $message['subject']));
        }
      }

      // Build array of sent results for spool table and reporting.
      if ($message['result']) {
        $result = array(
          'status' => SpoolStorageInterface::STATUS_DONE,
          'error' => FALSE,
        );
      }
      else {
        // This error may be caused by faulty mailserver configuration or overload.
        // Mark "pending" to keep trying.
        $result = array(
          'status' => SpoolStorageInterface::STATUS_PENDING,
          'error' => TRUE,
        );
      }
    }
    catch (SkipMailException $e) {
      $result = array(
        'status' => SpoolStorageInterface::STATUS_SKIPPED,
        'error' => FALSE,
      );
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function sendTest(NodeInterface $node, array $test_addresses) {
    // Force the current user to anonymous to ensure consistent permissions.
    $this->accountSwitcher->switchTo(new AnonymousUserSession());

    // Send the test newsletter to the test address(es) specified in the node.
    // Build array of test email addresses.

    // Send newsletter to test addresses.
    // Emails are send direct, not using the spool.
    $recipients = array('anonymous' => array(), 'user' => array());
    foreach ($test_addresses as $mail) {
      $mail = trim($mail);
      if (!empty($mail)) {
        $subscriber = simplenews_subscriber_load_by_mail($mail);
        if (!$subscriber) {
          // Create a stub subscriber. Use values from the user having the given
          // address, or if there is no such user, the anonymous user.
          if ($user = user_load_by_mail($mail)) {
            $subscriber = Subscriber::create()->fillFromAccount($user);
          }
          else {
            $subscriber = Subscriber::create(['mail' => $mail]);
          }
          // Keep the current language.
          $subscriber->setLangcode(\Drupal::languageManager()->getCurrentLanguage());
        }

        if ($subscriber->getUserId()) {
          $account = $subscriber->uid->entity;
          $recipients['user'][] = $account->getUserName() . ' <' . $mail . '>';
        }
        else {
          $recipients['anonymous'][] = $mail;
        }
        $mail = new MailEntity($node, $subscriber, \Drupal::service('simplenews.mail_cache'));
        $mail->setKey('test');
        $this->sendMail($mail);
      }
    }
    if (count($recipients['user'])) {
      $recipients_txt = implode(', ', $recipients['user']);
      drupal_set_message(t('Test newsletter sent to user %recipient.', array('%recipient' => $recipients_txt)));
    }
    if (count($recipients['anonymous'])) {
      $recipients_txt = implode(', ', $recipients['anonymous']);
      drupal_set_message(t('Test newsletter sent to anonymous %recipient.', array('%recipient' => $recipients_txt)));
    }

    $this->accountSwitcher->switchBack();
  }

  /**
   * {@inheritdoc}
   */
  public function sendCombinedConfirmation(SubscriberInterface $subscriber) {
    $params['from'] = $this->getFrom();
    $params['context']['simplenews_subscriber'] = $subscriber;
    // Send multiple if there is more than one change for this subscriber
    // single otherwise.
    $use_combined = $this->config->get('subscription.use_combined');
    $changes = $subscriber->getChanges();
    if ((count($changes) > 1 && $use_combined != 'never') || $use_combined == 'always') {
      $key = 'subscribe_combined';
      $this->mailManager->mail('simplenews', $key, $subscriber->getMail(), $subscriber->getLangcode(), $params, $params['from']['address']);
    }
    else {
      foreach ($changes as $newsletter_id => $key) {
        $params['context']['newsletter'] = simplenews_newsletter_load($newsletter_id);
        $this->mailManager->mail('simplenews', $key, $subscriber->getMail(), $subscriber->getLangcode(), $params, $params['from']['address']);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function updateSendStatus() {
    $counts = array(); // number of pending emails in the spool
    $sum = array(); // sum of emails in the spool per tnid (translation id)
    $send = array(); // nodes with the status 'send'

    // For each pending newsletter count the number of pending emails in the spool.
    $query = \Drupal::entityQuery('node');
    $nids = $query
      ->condition('simplenews_issue.status', SIMPLENEWS_STATUS_SEND_PENDING)
      ->execute();
    $nodes = Node::loadMultiple($nids);
    if ($nodes) {
      foreach ($nodes as $nid => $node) {
        $counts[$node->simplenews_issue->target_id][$nid] = \Drupal::service('simplenews.spool_storage')->countMails(array('entity_id' => $nid, 'entity_type' => 'node'));
      }
    }
    // Determine which nodes are send per translation group and per individual node.
    foreach ($counts as $newsletter_id => $node_count) {
      // The sum of emails per tnid is the combined status result for the group of translated nodes.
      // Untranslated nodes have tnid == 0 which will be ignored later.
      $sum[$newsletter_id] = array_sum($node_count);
      foreach ($node_count as $nid => $count) {
        // Translated nodes (tnid != 0)
        if ($newsletter_id != '0' && $sum[$newsletter_id] == '0') {
          $send[] = $nid;
        }
        // Untranslated nodes (tnid == 0)
        elseif ($newsletter_id == '0' && $count == '0') {
          $send[] = $nid;
        }
      }
    }

    // Update overall newsletter status
    if (!empty($send)) {
      foreach ($send as $nid) {
        $node = Node::load($nid);
        $node->simplenews_issue->status = SIMPLENEWS_STATUS_SEND_READY;
        $node->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFrom() {
    $address = $this->config->get('newsletter.from_address');
    $name = $this->config->get('newsletter.from_name');

    // Windows based PHP systems don't accept formatted email addresses.
    $formatted_address = (Unicode::substr(PHP_OS, 0, 3) == 'WIN') ? $address : '"'. addslashes(Unicode::mimeHeaderEncode($name)) .'" <'. $address .'>';

    return array(
      'address' => $address,
      'formatted' => $formatted_address,
    );
  }

  /**
   * Starts the execution timer.
   */
  protected function startTimer() {
    // Windows systems don't implement getrusage(). There is no alternative.
    if (!function_exists('getrusage')) {
      return;
    }

    $usage = getrusage();
    $this->startTime = (float)($usage['ru_stime.tv_sec'] . '.' . $usage['ru_stime.tv_usec']) + (float)($usage['ru_utime.tv_sec'] . '.' . $usage['ru_utime.tv_usec']);
  }

  /**
   * Returns the current execution time.
   *
   * @return float
   *   The elapsed PHP execution time since the last start.
   *
   * @see self::startTime()
   */
  protected function getCurrentExecutionTime() {
    // Windows systems don't implement getrusage(). There is no alternative.
    if (!function_exists('getrusage')) {
      return;
    }

    $usage = getrusage();
    $now = (float)($usage['ru_stime.tv_sec'] . '.' . $usage['ru_stime.tv_usec']) + (float)($usage['ru_utime.tv_sec'] . '.' . $usage['ru_utime.tv_usec']);

    return $now - $this->startTime;
  }

}
