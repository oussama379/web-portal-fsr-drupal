<?php

namespace Drupal\simplenews\Spool;

use Drupal\node\NodeInterface;

/**
 * The spool storage manages a queue of mails that need to be sent.
 */
interface SpoolStorageInterface {

  /**
   * On Hold.
   */
  const STATUS_HOLD = 0;

  /**
   * Pending.
   */
  const STATUS_PENDING = 1;

  /**
   * Mail spool entry is done.
   */
  const STATUS_DONE = 2;

  /**
   * In progress and locked until expired.
   */
  const STATUS_IN_PROGRESS = 3;

  /**
   * Marks a spool entry as skipped (not sent, but done).
   */
  const STATUS_SKIPPED = 4;

  /**
   * Used when sending an unlimited amount of mails from the spool.
   */
  const UNLIMITED = -1;

  /**
   * This function allocates mails to be sent in current run.
   *
   * Drupal acquire_lock guarantees that no concurrency issue happened.
   * If the message status is SpoolStorageInterface::STATUS_IN_PROGRESS but the
   * maximum send time has expired, the mail id will be returned as a mail which
   * is not allocated to another process.
   *
   * @param int $limit
   *   (Optional) The maximum number of mails to load from the spool. Defaults
   *   to unlimited.
   * @param array $conditions
   *   (Optional) Array of conditions which are applied to the query. If not
   *   set, status defaults to SpoolStorageInterface::STATUS_PENDING,
   *   SpoolStorageInterface::STATUS_IN_PROGRESS.
   *
   * @return \Drupal\simplenews\Spool\SpoolListInterface
   *   A mail spool list.
   */
  function getMails($limit = self::UNLIMITED, $conditions = array());

  /**
   * Update status of mail data in spool table.
   *
   * Time stamp is set to current time.
   *
   * @param array $msids
   *   Array of Mail spool ids to be updated
   * @param array $data
   *   Array containing email sent results, with the following keys:
   *   - status: Any of the status constants.
   *   - error: (optional) The error id.  Defaults to 0 (no error).
   */
  function updateMails($msids, array $data);

  /**
   * Count data in mail spool table.
   *
   * @param array $conditions
   *   (Optional) Array of conditions which are applied to the query. Defaults
   *
   * @return int
   *   Count of mail spool elements of the passed in arguments.
   */
  function countMails(array $conditions = array());

  /**
   * Remove old records from mail spool table.
   *
   * All records with status 'send' and time stamp before the expiration date
   * are removed from the spool.
   *
   * @return int
   *   Number of deleted spool rows.
   */
  function clear();

  /**
   * Remove records from mail spool table according to the conditions.
   *
   * @return int
   *   Count deleted
   */
  function deleteMails(array $conditions);

  /**
   * Add the newsletter node to the mail spool.
   *
   * The caller is responsible for saving the changed node entity.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The newsletter node to be sent.
   */
  function addFromEntity(NodeInterface $node);

  /**
   * Save mail message in mail cache table.
   *
   * @param array $spool
   *   The message to be stored in the spool table, as an array containing the
   *   following keys:
   *   - mail
   *   - nid
   *   - tid
   *   - status: (optional) Defaults to SpoolStorageInterface::STATUS_PENDING
   *   - time: (optional) Defaults to REQUEST_TIME.
   */
  function addMail(array $spool);

}
