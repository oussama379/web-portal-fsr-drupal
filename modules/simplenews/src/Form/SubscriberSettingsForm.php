<?php

namespace Drupal\simplenews\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure simplenews newsletter settings.
 */
class SubscriberSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simplenews_admin_settings_subscriber';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['simplenews.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('simplenews.settings');
    $form['account'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('User account'),
      '#collapsible' => FALSE,
    );
    $form['account']['simplenews_sync_fields'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Synchronize between account and subscriber fields'),
      '#default_value' => $config->get('subscriber.sync_fields'),
      '#description' => $this->t('<p>When checked fields that exist with identical name and type on subscriber and accounts will be synchronized.</p>'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('simplenews.settings')
      ->set('subscriber.sync_fields', $form_state->getValue('simplenews_sync_fields'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
