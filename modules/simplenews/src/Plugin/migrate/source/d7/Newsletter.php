<?php

namespace Drupal\simplenews\Plugin\migrate\source\d7;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Migration source for Newsletter entities in D7.
 *
 * @MigrateSource(
 *   id = "simplenews_newsletter"
 * )
 */
class Newsletter extends DrupalSqlBase {
  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'newsletter_id' => $this->t('Newsletter ID'),
      'name' => $this->t('Name'),
      'description' => $this->t('Description'),
      'format' => $this->t('HTML or plaintext'),
      'priority' => $this->t('Priority'),
      'receipt' => $this->t('Request read receipt'),
      'from_name' => $this->t('Name of the e-mail author'),
      'email_subject' => $this->t('Newsletter subject'),
      'from_address' => $this->t('E-mail author address'),
      'hyperlinks' => $this->t('Indicates if hyperlinks should be kept inline or extracted'),
      'new_account' => $this->t('Indicates how to integrate with the register form'),
      'opt_inout' => $this->t('Defines the Opt-In/out options'),
      'block' => $this->t('TRUE if a block should be provided for this newsletter'),
      'weight' => $this->t('Weight of the newsletter when displayed in listings'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['newsletter_id' => ['type' => 'serial']];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('simplenews_newsletter', 'n')
      ->fields('n')
      ->orderBy('newsletter_id');
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
