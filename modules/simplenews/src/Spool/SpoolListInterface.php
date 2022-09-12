<?php

namespace Drupal\simplenews\Spool;

/**
 * A list of spooled mails.
 */
interface SpoolListInterface extends \Countable {

  /**
   * Returns a Simplenews source to be sent.
   *
   * A single source may represent any number of mail spool rows, e.g. by
   * addressing them as BCC.
   *
   * @return \Drupal\simplenews\Mail\MailInterface
   */
  function nextMail();

  /**
   * Returns the processed mail spool rows, keyed by the msid.
   *
   * Only rows that were processed while preparing the previously returned
   * source must be returned.
   *
   * @return
   *   An array of mail spool rows, keyed by the msid. Can optionally have set
   *   the following additional properties.
   *     - actual_nid: In case of content translation, the source node that was
   *       used for this mail.
   *     - error: FALSE if the prepration for this row failed. For example set
   *       when the corresponding node failed to load.
   *     - status: A simplenews spool status to indicate the status.
   */
  function getProcessed();
}
