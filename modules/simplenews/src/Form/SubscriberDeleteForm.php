<?php

namespace Drupal\simplenews\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete a contact category.
 */
class SubscriberDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('view.simplenews_subscribers.page_1');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message(t('Subscriber %label has been deleted.', array('%label' => $this->entity->label())));
    \Drupal::logger('simplenews')->notice('Subscriber %label has been deleted.', array('%label' => $this->entity->label()));
    $form_state->setRedirect('view.simplenews_subscribers.page_1');
  }

}
