<?php

namespace Drupal\simplenews\Plugin\migrate\source\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Migration source for Subscriber entries in D7.
 *
 * @MigrateSource(
 *   id = "simplenews_subscriber"
 * )
 */
class Subscriber extends DrupalSqlBase {
  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'snid' => $this->t('Subscriber ID'),
      'activated' => $this->t('Activated'),
      'mail' => $this->t('Subscriber\'s e-mail address'),
      'uid' => $this->t('Corresponding user'),
      'language' => $this->t('Language'),
      'changes' => $this->t('Pending unconfirmed subscription changes'),
      'created' => $this->t('Time of creation'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['snid' => ['type' => 'serial']];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('simplenews_subscriber', 's')
      ->fields('s')
      ->orderBy('snid');
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $result = parent::prepareRow($row);

    // Add associated data from the subscriptions table.
    $subscriptions = $this->select('simplenews_subscription', 'sub')
      ->fields('sub', ['newsletter_id', 'status', 'timestamp', 'source'])
      ->condition('sub.snid', $row->getSourceProperty('snid'))
      ->execute()
      ->fetchAllAssoc('newsletter_id');
    $row->setSourceProperty('subscriptions', $subscriptions);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $this->dependencies = parent::calculateDependencies();
    // Declare dependency to the provider of the base class.
    $this->addDependency('module', 'migrate_drupal');
    return $this->dependencies;
  }

}
