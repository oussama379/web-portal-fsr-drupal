<?php

namespace Drupal\simplenews\Spool;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\Condition;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\node\NodeInterface;

/**
 * Default database spool storage.
 */
class SpoolStorage implements SpoolStorageInterface {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Creates a SpoolStorage object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface
   *   The module handler.
   */
  public function __construct(Connection $connection, LockBackendInterface $lock, ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler) {
    $this->connection = $connection;
    $this->lock = $lock;
    $this->config = $config_factory->get('simplenews.settings');
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getMails($limit = self::UNLIMITED, $conditions = array()) {
    $messages = array();

    // Continue to support 'nid' as a condition.
    if (!empty($conditions['nid'])) {
      $conditions['entity_type'] = 'node';
      $conditions['entity_id'] = $conditions['nid'];
      unset($conditions['nid']);
    }

    // Add default status condition if not set.
    if (!isset($conditions['status'])) {
      $conditions['status'] = array(SpoolStorageInterface::STATUS_PENDING, SpoolStorageInterface::STATUS_IN_PROGRESS);
    }

    // Special case for the status condition, the in progress actually only
    // includes spool items whose locking time has expired. So this need to build
    // an OR condition for them.
    $status_or = new Condition('OR');
    $statuses = is_array($conditions['status']) ? $conditions['status'] : array($conditions['status']);
    foreach ($statuses as $status) {
      if ($status == SpoolStorageInterface::STATUS_IN_PROGRESS) {
        $status_or->condition((new Condition('AND'))
          ->condition('status', $status)
          ->condition('s.timestamp', $this->getExpirationTime(), '<')
        );
      }
      else {
        $status_or->condition('status', $status);
      }
    }
    unset($conditions['status']);

    $query = $this->connection->select('simplenews_mail_spool', 's')
      ->fields('s')
      ->condition($status_or)
      ->orderBy('s.timestamp', 'ASC');

    // Add conditions.
    foreach ($conditions as $field => $value) {
      $query->condition($field, $value);
    }

    /* BEGIN CRITICAL SECTION */
    // The semaphore ensures that multiple processes get different message ID's,
    // so that duplicate messages are not sent.
    if ($this->lock->acquire('simplenews_acquire_mail')) {
      // Get message id's
      // Allocate messages
      if ($limit > 0) {
        $query->range(0, $limit);
      }
      foreach ($query->execute() as $message) {
        if (Unicode::strlen($message->data)) {
          $message->data = unserialize($message->data);
        }
        else {
          $message->data = simplenews_subscriber_load_by_mail($message->mail);
        }
        $messages[$message->msid] = $message;
      }
      if (count($messages) > 0) {
        // Set the state and the timestamp of the messages
        $this->updateMails(
          array_keys($messages), array('status' => SpoolStorageInterface::STATUS_IN_PROGRESS)
        );
      }

      $this->lock->release('simplenews_acquire_mail');
    }

    /* END CRITICAL SECTION */

    return new SpoolList($messages);
  }

  /**
   * {@inheritdoc}
   */
  public function updateMails($msids, array $data) {
    $this->connection->update('simplenews_mail_spool')
      ->condition('msid', (array) $msids, 'IN')
      ->fields(array(
        'status' => $data['status'],
        'error' => isset($result['error']) ? (int) $data['error'] : 0,
        'timestamp' => REQUEST_TIME,
      ))
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function countMails(array $conditions = array()) {

    // Continue to support 'nid' as a condition.
    if (!empty($conditions['nid'])) {
      $conditions['entity_type'] = 'node';
      $conditions['entity_id'] = $conditions['nid'];
      unset($conditions['nid']);
    }

    // Add default status condition if not set.
    if (!isset($conditions['status'])) {
      $conditions['status'] = array(SpoolStorageInterface::STATUS_PENDING, SpoolStorageInterface::STATUS_IN_PROGRESS);
    }

    $query = $this->connection->select('simplenews_mail_spool');
    // Add conditions.
    foreach ($conditions as $field => $value) {
      if ($field == 'status') {
        if (!is_array($value)) {
          $value = array($value);
        }
        $status_or = new Condition('OR');
        foreach ($value as $status) {
          // Do not count pending entries unless they are expired.
          if ($status == SpoolStorageInterface::STATUS_IN_PROGRESS) {
            $status_or->condition((new Condition('AND'))
              ->condition('status', $status)
              ->condition('timestamp', $this->getExpirationTime(), '<')
            );
          }
          else {
            $status_or->condition('status', $status);
          }
        }
        $query->condition($status_or);
      }
      else {
        $query->condition($field, $value);
      }
    }

    $query->addExpression('COUNT(*)', 'count');

    return (int)$query
      ->execute()
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clear() {

    $expiration_time = REQUEST_TIME - $this->config->get('mail.spool_expire') * 86400;
    return $this->connection->delete('simplenews_mail_spool')
      ->condition('status', [SpoolStorageInterface::STATUS_DONE, SpoolStorageInterface::STATUS_SKIPPED], 'IN')
      ->condition('timestamp', $expiration_time, '<=')
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMails(array $conditions) {
    // Continue to support 'nid'.
    if (!empty($conditions['nid'])) {
      $conditions['entity_type'] = 'node';
      $conditions['entity_id'] = $conditions['nid'];
      unset($conditions['nid']);
    }

    $query = $this->connection->delete('simplenews_mail_spool');

    foreach ($conditions as $condition => $value) {
      $query->condition($condition, $value);
    }
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function addFromEntity(NodeInterface $node) {
    $newsletter = $node->simplenews_issue->entity;
    $handler = $node->simplenews_issue->handler;
    $handler_settings = $node->simplenews_issue->handler_settings;

    $recipient_handler = simplenews_get_recipient_handler($newsletter, $handler, $handler_settings);

    // To send the newsletter, the node id and target email addresses
    // are stored in the spool.
    // Only subscribed recipients are stored in the spool (status = 1).
    $select = $recipient_handler->buildRecipientQuery();
    $select->addExpression('\'node\'', 'entity_type');
    $select->addExpression($node->id(), 'entity_id');
    $select->addExpression(SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, 'status');
    $select->addExpression(REQUEST_TIME, 'timestamp');

    $node->simplenews_issue->subscribers = simplenews_count_subscriptions($node->simplenews_issue->target_id);

    $this->connection->insert('simplenews_mail_spool')
      ->from($select)
      ->execute();

    // Update simplenews newsletter status to send pending.
    $node->simplenews_issue->status = SIMPLENEWS_STATUS_SEND_PENDING;

    // Notify other modules that a newsletter was just spooled.
    $this->moduleHandler->invokeAll('simplenews_spooled', array($node));
  }

  /**
   * {@inheritdoc}
   */
  public function addMail(array $spool) {
    $status = isset($spool['status']) ? $spool['status'] : SpoolStorageInterface::STATUS_PENDING;
    $time = isset($spool['time']) ? $spool['time'] : REQUEST_TIME;

    $this->connection->insert('simplenews_mail_spool')
      ->fields(array(
        'mail' => $spool['mail'],
        'entity_type' => $spool['entity_type'],
        'entity_id' => $spool['entity_id'],
        'newsletter_id' => $spool['newsletter_id'],
        'snid' => $spool['snid'],
        'status' => $status,
        'timestamp' => $time,
        'data' => serialize($spool['data']),
      ))
      ->execute();
  }

  /**
   * Returns the expiration time for IN_PROGRESS status.
   *
   * @return int
   *   A unix timestamp. Any IN_PROGRESS messages with a timestamp older than
   *   this will be re-allocated and re-sent.
   */
  protected function getExpirationTime() {
    $timeout = $this->config->get('mail.spool_progress_expiration');
    $expiration_time = REQUEST_TIME - $timeout;
    return $expiration_time;
  }

}
