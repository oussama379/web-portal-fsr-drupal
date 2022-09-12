<?php

namespace Drupal\simplenews\Plugin\monitoring\SensorPlugin;

use Drupal\monitoring\Result\SensorResultInterface;
use Drupal\monitoring\SensorPlugin\SensorPluginBase;
use Drupal\simplenews\Spool\SpoolStorageInterface;

/**
 * Monitors pending items in the simplenews mail spool.
 *
 * @SensorPlugin(
 *   id = "simplenews_pending",
 *   label = @Translation("Simplenews Pending"),
 *   description = @Translation("Monitors pending items in the simplenews mail spool."),
 *   provider = "simplenews",
 *   addable = FALSE
 * )
 *
 * Once all is processed, the value should be 0.
 *
 * @see simplenews_count_spool()
 */
class PendingSensorPlugin extends SensorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function runSensor(SensorResultInterface $result) {
    $result->setValue(\Drupal::service('simplenews.spool_storage')->countMails(array('status' => SpoolStorageInterface::STATUS_PENDING)));
  }
}
