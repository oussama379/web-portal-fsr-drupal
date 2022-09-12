<?php

/**
 * @file
 * Contains \Drupal\field_collection\Form\FieldCollectionItemDeleteForm.
 */

namespace Drupal\field_collection\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for deleting a field collection item.
 */
class FieldCollectionItemDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete this %title?', array('%title' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->entity->getHost()->toUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $host = $this->entity->getHost();
    foreach ($host->{$this->entity->bundle()} as $key => $value) {
      if ($value->value == $this->entity->id()) {
        unset($host->{$this->entity->bundle()}[$key]);
      }
    }
    $host->save();
    $this->entity->delete();

    $this->logger('content')->notice('@type: deleted %id.', array(
      '@type' => $this->entity->bundle(),
      '%id' => $this->entity->id())
    );

    $node_type_storage = $this->entityTypeManager->getStorage('field_collection');
    $node_type = $node_type_storage->load($this->entity->bundle())->label();

    drupal_set_message(t('@type %id has been deleted.', array(
      '@type' => $node_type,
      '%id' => $this->entity->id())));

    $form_state->setRedirectUrl($host->toUrl());
  }

}
