<?php

namespace Drupal\simplenews\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\ContentEntityForm;

/**
 * Form controller for the subscriber edit forms.
 *
 * The acting user is someone with administrative privileges managing other
 * users (not themselves).
 */
class SubscriberForm extends ContentEntityForm {

  /**
   * Overrides Drupal\Core\Entity\EntityForm::form().
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $this->getSubscriptionWidget($form_state)
      ->setAvailableNewsletterIds(array_keys(simplenews_newsletter_get_visible()));
    /* @var \Drupal\simplenews\SubscriberInterface $subscriber */
    $subscriber = $this->entity;

    $form['#title'] = $this->t('Edit subscriber @mail', array('@mail' => $subscriber->getMail()));

    $form['activated'] = array(
      '#title' => t('Status'),
      '#type' => 'fieldset',
      '#description' => t('Active or inactive account.'),
      '#weight' => 15,
    );
    $form['activated']['status'] = array(
      '#type' => 'checkbox',
      '#title' => t('Active'),
      '#default_value' => $subscriber->getStatus(),
    );

    $language_manager = \Drupal::languageManager();
    if ($language_manager->isMultilingual()) {
      $languages = $language_manager->getLanguages();
      foreach ($languages as $langcode => $language) {
        $language_options[$langcode] = $language->getName();
      }
      $form['language'] = array(
        '#type' => 'fieldset',
        '#title' => t('Preferred language'),
        '#description' => t('The e-mails will be localized in language chosen. Real users have their preference in account settings.'),
        '#disabled' => FALSE,
      );
      if ($subscriber->getUserId()) {
        // Fallback if user has not defined a language.
        $form['language']['langcode'] = array(
          '#type' => 'item',
          '#title' => t('User language'),
          '#markup' => $subscriber->language()->getName(),
        );
      }
      else {
        $form['language']['langcode'] = array(
          '#type' => 'select',
          '#default_value' => $subscriber->language()->getId(),
          '#options' => $language_options,
          '#required' => TRUE,
        );
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    // Switch label to Subscribe for new subscribers.
    if ($this->entity->isNew()) {
      $actions['submit']['#value'] = $this->t('Subscribe');
    }
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // The subscriptions field has properties which are set to NULL by ordinary
    // saving, which is wrong. The Subscriber::(un)subscribe() methods save the
    // values correctly. For each newsletter ID we check if it exists in
    // current subscriptions and new subscriptions respectively.
    $current_subscriptions =  $this->entity->getSubscribedNewsletterIds();
    $subscription_values = $form_state->getValue('subscriptions');
    $new_subscriptions = array();
    foreach ($subscription_values as $subscription_value) {
      array_push($new_subscriptions, $subscription_value['target_id']);
    }
    foreach (array_keys(simplenews_newsletter_get_visible()) as $newsletter) {
      if (in_array($newsletter, $current_subscriptions) && !in_array($newsletter, $new_subscriptions)) {
        $this->entity->unsubscribe($newsletter);
      } elseif (!in_array($newsletter, $current_subscriptions) && in_array($newsletter, $new_subscriptions)) {
        $this->entity->subscribe($newsletter);
      }
    }

    $form_state->setRedirect('view.simplenews_subscribers.page_1');

    if ($this->entity->isNew()) {
      drupal_set_message($this->t('Subscriber %label has been added.', array('%label' => $this->entity->label())));
    } else {
      drupal_set_message($this->t('Subscriber %label has been updated.', array('%label' => $this->entity->label())));
    }
  }

  /**
   * Returns the renderer for the 'subscriptions' field.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return \Drupal\simplenews\SubscriptionWidgetInterface
   *   The widget.
   */
  protected function getSubscriptionWidget(FormStateInterface $form_state) {
    return $this->getFormDisplay($form_state)->getRenderer('subscriptions');
  }
}
