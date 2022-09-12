<?php

namespace Drupal\simplenews\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Removes fields and data used by Simplenews.
 */
class PrepareUninstallForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simplenews_admin_settings_prepare_uninstall';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['simplenews'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Prepare uninstall'),
      '#description' => $this->t('When clicked all Simplenews data (content, fields) will be removed.'),
    );

    $form['simplenews']['prepare_uninstall'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Delete Simplenews data'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $batch = [
      'title' => t('Deleting subscribers'),
      'operations' => [
        [
          [__CLASS__, 'deleteSubscribers'], [],
        ],
        [
          [__CLASS__, 'removeFields'], [],
        ],
        [
          [__CLASS__, 'purgeFieldData'], [],
        ],
      ],
      'progress_message' => static::t('Deleting Simplenews data... Completed @percentage% (@current of @total).'),
    ];
    batch_set($batch);

    drupal_set_message($this->t('Simplenews data has been deleted.'));
  }

  /**
   * Deletes Simplenews subscribers.
   */
  public static function deleteSubscribers(&$context) {
    $subscriber_ids = \Drupal::entityQuery('simplenews_subscriber')->range(0, 100)->execute();
    $storage = \Drupal::entityManager()->getStorage('simplenews_subscriber');
    if ($subscribers = $storage->loadMultiple($subscriber_ids)) {
      $storage->delete($subscribers);
    }
    $context['finished'] = (int) count($subscriber_ids) < 100;
  }

  /**
   * Removes Simplenews fields.
   */
  public static function removeFields() {
    $simplenews_fields_ids = \Drupal::entityQuery('field_config')->condition('field_type', 'simplenews_', 'STARTS_WITH')->execute();
    $simplenews_fields = \Drupal::entityManager()->getStorage('field_config')->loadMultiple($simplenews_fields_ids);
    $field_config_storage = \Drupal::entityManager()->getStorage('field_config');
    $field_config_storage->delete($simplenews_fields);
  }

  /**
   * Purges a field data.
   */
  public static function purgeFieldData() {
    do {
      field_purge_batch(1000);
      $properties = array(
        'deleted' => TRUE,
        'include_deleted' => TRUE,
      );
      $fields = entity_load_multiple_by_properties('field_config', $properties);
    } while ($fields);
  }

}
