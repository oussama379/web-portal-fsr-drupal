<?php

namespace Drupal\simplenews\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\simplenews\Plugin\Field\FieldType\SubscriptionItem;
use Drupal\simplenews\SubscriberInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Defines the simplenews subscriber entity.
 *
 * @ContentEntityType(
 *   id = "simplenews_subscriber",
 *   label = @Translation("Simplenews subscriber"),
 *   handlers = {
 *     "storage" = "Drupal\simplenews\Subscription\SubscriptionStorage",
 *     "form" = {
 *       "default" = "Drupal\simplenews\Form\SubscriberForm",
 *       "account" = "Drupal\simplenews\Form\SubscriptionsAccountForm",
 *       "block" = "Drupal\simplenews\Form\SubscriptionsBlockForm",
 *       "page" = "Drupal\simplenews\Form\SubscriptionsPageForm",
 *       "delete" = "Drupal\simplenews\Form\SubscriberDeleteForm",
 *     },
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\simplenews\SubscriberViewsData"
 *   },
 *   base_table = "simplenews_subscriber",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "mail"
 *   },
 *   field_ui_base_route = "simplenews.settings_subscriber",
 *   admin_permission = "administer simplenews subscriptions",
 *   links = {
 *     "edit-form" = "/admin/people/simplenews/edit/{simplenews_subscriber}",
 *     "delete-form" = "/admin/people/simplenews/delete/{simplenews_subscriber}",
 *   }
 * )
 */
class Subscriber extends ContentEntityBase implements SubscriberInterface {

  /**
   * Whether currently copying field values to corresponding User.
   *
   * @var bool
   */
  protected static $syncing;

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->get('message')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($message) {
    $this->set('message', $message);
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
  }

  /**
   * {@inheritdoc}
   */
  public function getMail() {
    return $this->get('mail')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setMail($mail) {
    $this->set('mail', $mail);
  }

  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    $value = $this->get('uid')->getValue();
    if (isset($value[0]['target_id'])) {
      return $value[0]['target_id'];
    }
    return '0';
  }

  /**
   * {@inheritdoc}
   */
  public function getUser() {
    $mail = $this->getMail();

    if (empty($mail)) {
      return NULL;
    }
    if ($user = User::load($this->getUserId())) {
      return $user;
    }
    else {
      return user_load_by_mail($this->getMail()) ?: NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setUserId($uid) {
    $this->set('uid', $uid);
  }

  /**
   * {@inheritdoc}
   */
  public function getLangcode() {
    return $this->get('langcode')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLangcode($langcode) {
    $this->set('langcode', $langcode);
  }

  /**
   * {@inheritdoc}
   */
  public function fillFromAccount(AccountInterface $account) {
    $this->setUserId($account->id());
    $this->setMail($account->getEmail());
    $this->setLangcode($account->getPreferredLangcode());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChanges() {
    return unserialize($this->get('changes')->value);
  }

  /**
   * {@inheritdoc}
   */
  public function setChanges($changes) {
    $this->set('changes', serialize($changes));
  }

  /**
   * {@inheritdoc}
   */
  public function isSyncing() {
    return static::$syncing;
  }

  /**
   * {@inheritdoc}
   */
  public function isSubscribed($newsletter_id) {
    foreach ($this->subscriptions as $item) {
      if ($item->target_id == $newsletter_id) {
        return $item->status == SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isUnsubscribed($newsletter_id) {
    foreach ($this->subscriptions as $item) {
      if ($item->target_id == $newsletter_id) {
        return $item->status == SIMPLENEWS_SUBSCRIPTION_STATUS_UNSUBSCRIBED;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscription($newsletter_id) {
    foreach ($this->subscriptions as $item) {
      if ($item->target_id == $newsletter_id) {
        return $item;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscribedNewsletterIds() {
    $ids = array();
    foreach ($this->subscriptions as $item) {
      if ($item->status == SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED) {
        $ids[] = $item->target_id;
      }
    }
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function subscribe($newsletter_id, $status = SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, $source = 'unknown', $timestamp = REQUEST_TIME) {
    if ($subscription = $this->getSubscription($newsletter_id)) {
      $subscription->status = $status;
    }
    else {
      $data = array(
        'target_id' => $newsletter_id,
        'status' => $status,
        'source' => $source,
        'timestamp' => $timestamp,
      );
      $this->subscriptions->appendItem($data);
    }
    if ($status == SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED) {
      \Drupal::moduleHandler()->invokeAll('simplenews_subscribe', array($this, $newsletter_id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function unsubscribe($newsletter_id, $source = 'unknown', $timestamp = REQUEST_TIME) {
    if ($subscription = $this->getSubscription($newsletter_id)) {
      $subscription->status = SIMPLENEWS_SUBSCRIPTION_STATUS_UNSUBSCRIBED;
    }
    else {
      $data = array(
        'target_id' => $newsletter_id,
        'status' => SIMPLENEWS_SUBSCRIPTION_STATUS_UNSUBSCRIBED,
        'source' => $source,
        'timestamp' => $timestamp,
      );
      $this->subscriptions->appendItem($data);
    }
    // Clear eventually existing mail spool rows for this subscriber.
    \Drupal::service('simplenews.spool_storage')->deleteMails(array('snid' => $this->id(), 'newsletter_id' => $newsletter_id));

    \Drupal::moduleHandler()->invokeAll('simplenews_unsubscribe', array($this, $newsletter_id));
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Copy values for shared fields to existing user.
    if (\Drupal::config('simplenews.settings')->get('subscriber.sync_fields') && $user = $this->getUser()) {
      static::$syncing = TRUE;
      foreach ($this->getUserSharedFields($user) as $field_name) {
        $user->set($field_name, $this->get($field_name)->getValue());
      }
      $user->save();
      static::$syncing = FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postCreate(EntityStorageInterface $storage) {
    parent::postCreate($storage);

    // Set the uid field if there is a user with the same email.
    $user_ids = \Drupal::entityQuery('user')
      ->condition('mail', $this->getMail())
      ->execute();
    if (!empty($user_ids)) {
      $this->setUserId(array_pop($user_ids));
    }

    // Copy values for shared fields from existing user.
    if (\Drupal::config('simplenews.settings')->get('subscriber.sync_fields') && $user = $this->getUser()) {
      foreach ($this->getUserSharedFields($user) as $field_name) {
        $this->set($field_name, $user->get($field_name)->getValue());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserSharedFields(UserInterface $user) {
    $field_names = array();
    // Find any fields sharing name and type.
    foreach ($this->getFieldDefinitions() as $field_definition) {
      /** @var \Drupal\Core\Field\FieldDefinitionInterface $field_definition */
      $field_name = $field_definition->getName();
      $user_field = $user->getFieldDefinition($field_name);
      if ($field_definition->getTargetBundle() && isset($user_field) && $user_field->getType() == $field_definition->getType()) {
        $field_names[] = $field_name;
      }
    }
    return $field_names;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Subscriber ID'))
      ->setDescription(t('Primary key: Unique subscriber ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The subscriber UUID.'))
      ->setReadOnly(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDescription(t('Boolean indicating the status of the subscriber.'))
      ->setDefaultValue(TRUE);

    $fields['mail'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t("The subscriber's email address."))
      ->setSetting('default_value', '')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'email',
        'settings' => array(),
        'weight' => 5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The corresponding user.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('form', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language'))
      ->setDescription(t("The subscriber's preferred language."));

    $fields['changes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Changes'))
      ->setDescription(t('Contains the requested subscription changes.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the subscriber was created.'));

    return $fields;
  }

}
