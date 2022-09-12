<?php

namespace Drupal\simplenews\Mail;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Render\Markup;
use Drupal\file\Entity\File;
use Drupal\simplenews\SubscriberInterface;
use Drupal\user\Entity\User;

/**
 * Default mail class for entities.
 */
class MailEntity implements MailInterface {
  use DependencySerializationTrait;

  /**
   * The entity object.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entity;

  /**
   * The cached build render array.
   *
   * @var array
   */
  protected $build;

  /**
   * The newsletter.
   *
   * @var \Drupal\simplenews\NewsletterInterface
   */
  protected $newsletter;

  /**
   * The subscriber and therefore recipient of this mail.
   *
   * @var \Drupal\simplenews\SubscriberInterface
   */
  protected $subscriber;

  /**
   * The mail key used for mails.
   *
   * @var string
   */
  protected $key = 'test';

  /**
   * Cache implementation used for this mail.
   *
   * @var MailCacheInterface
   */
  protected $cache;

  /**
   * Constructs a MailEntity object.
   */
  public function __construct(ContentEntityInterface $entity, SubscriberInterface $subscriber, MailCacheInterface $mail_cache) {
    $this->setSubscriber($subscriber);
    $this->setEntity($entity);
    $this->cache = $mail_cache;
    $this->newsletter = $entity->simplenews_issue->entity;
  }

  /**
   * Set the entity of this mail.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity of this mail.
   */
  public function setEntity(ContentEntityInterface $entity) {
    $this->entity = $entity;
    if ($this->entity->hasTranslation($this->getLanguage())) {
      $this->entity = $this->entity->getTranslation($this->getLanguage());
    }
  }

  /**
   * Returns the corresponding newsletter.
   *
   * @return \Drupal\simplenews\NewsletterInterface
   *   The newsletter.
   */
  public function getNewsletter() {
    return $this->newsletter;
  }

  /**
   * Set the active subscriber.
   *
   * @param \Drupal\simplenews\SubscriberInterface $subscriber
   *   The active subscriber.
   */
  public function setSubscriber(SubscriberInterface $subscriber) {
    $this->subscriber = $subscriber;
  }

  /**
   * Return the subscriber object.
   *
   * @return \Drupal\simplenews\SubscriberInterface
   *   The subscriber object.
   */
  public function getSubscriber() {
    return $this->subscriber;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeaders(array $headers) {

    // If receipt is requested, add headers.
    if ($this->newsletter->receipt) {
      $headers['Disposition-Notification-To'] = $this->getFromAddress();
      $headers['X-Confirm-Reading-To'] = $this->getFromAddress();
    }

    // Add priority if set.
    switch ($this->newsletter->priority) {
      case SIMPLENEWS_PRIORITY_HIGHEST:
        $headers['Priority'] = 'High';
        $headers['X-Priority'] = '1';
        $headers['X-MSMail-Priority'] = 'Highest';
        break;
      case SIMPLENEWS_PRIORITY_HIGH:
        $headers['Priority'] = 'urgent';
        $headers['X-Priority'] = '2';
        $headers['X-MSMail-Priority'] = 'High';
        break;
      case SIMPLENEWS_PRIORITY_NORMAL:
        $headers['Priority'] = 'normal';
        $headers['X-Priority'] = '3';
        $headers['X-MSMail-Priority'] = 'Normal';
        break;
      case SIMPLENEWS_PRIORITY_LOW:
        $headers['Priority'] = 'non-urgent';
        $headers['X-Priority'] = '4';
        $headers['X-MSMail-Priority'] = 'Low';
        break;
      case SIMPLENEWS_PRIORITY_LOWEST:
        $headers['Priority'] = 'non-urgent';
        $headers['X-Priority'] = '5';
        $headers['X-MSMail-Priority'] = 'Lowest';
        break;
    }

    // Add user specific header data.
    $headers['From'] = $this->getFromFormatted();
    $headers['List-Unsubscribe'] = '<' . \Drupal::token()->replace('[simplenews-subscriber:unsubscribe-url]', $this->getTokenContext(), array('sanitize' => FALSE)) . '>';

    // Add general headers
    $headers['Precedence'] = 'bulk';
    return $headers;
  }

  /**
   * {@inheritdoc}
   */
  function getTokenContext() {
    return array(
      'newsletter' => $this->getNewsletter(),
      'simplenews_subscriber' => $this->getSubscriber(),
      $this->getEntity()->getEntityTypeId() => $this->getEntity(),
    );
  }

  /**
   * {@inheritdoc}
   */
  function setKey($key) {
    $this->key = $key;
  }

  /**
   * {@inheritdoc}
   */
  function getKey() {
    return $this->key;
  }

  /**
   * {@inheritdoc}
   */
  function getFromFormatted() {
    // Windows based PHP systems don't accept formatted email addresses.
    if (Unicode::substr(PHP_OS, 0, 3) == 'WIN') {
      return $this->getFromAddress();
    }
    return '"' . addslashes(Unicode::mimeHeaderEncode($this->getNewsletter()->from_name)) . '" <' . $this->getFromAddress() . '>';
  }

  /**
   * {@inheritdoc}
   */
  function getFromAddress() {
    return $this->getNewsletter()->from_address;
  }

  /**
   * {@inheritdoc}
   */
  function getRecipient() {
    return $this->getSubscriber()->getMail();
  }

  /**
   * {@inheritdoc}
   */
  function getFormat() {
    return $this->getNewsletter()->format;
  }

  /**
   * {@inheritdoc}
   */
  function getLanguage() {
    return $this->getSubscriber()->getLangcode();
  }

  /**
   * {@inheritdoc}
   */
  function getEntity() {
    return $this->entity;
  }

  /**
   * {@inheritdoc}
   */
  function getSubject() {
    // Build email subject and perform some sanitizing.
    // Use the requested language if enabled.
    $langcode = $this->getLanguage();
    $subject = \Drupal::token()->replace($this->getNewsletter()->subject, $this->getTokenContext(), array('sanitize' => FALSE, 'langcode' => $langcode));

    // Line breaks are removed from the email subject to prevent injection of
    // malicious data into the email header.
    $subject = str_replace(array("\r", "\n"), '', $subject);
    return $subject;
  }

  /**
   * Set up the necessary language and user context.
   */
  protected function setContext() {

    // Switch to the user
    if ($this->uid = $this->getSubscriber()->getUserId()) {
      \Drupal::service('account_switcher')->switchTo(User::load($this->uid));
    }

    // Change language if the requested language is enabled.
    /*$language = $this->getLanguage();
    $languages = LanguageManagerInterface::getLanguages();
    if (isset($languages[$language])) {
      $this->original_language = \Drupal::languageManager()->getCurrentLanguage();
      $GLOBALS['language'] = $languages[$language];
      $GLOBALS['language_url'] = $languages[$language];
      // Overwrites the current content language for i18n_select.
      if (\Drupal::moduleHandler()->moduleExists('i18n_select')) {
        $GLOBALS['language_content'] = $languages[$language];
      }
    }*/
  }

  /**
   * Reset the context.
   */
  protected function resetContext() {

    // Switch back to the previous user.
    if ($this->uid) {
      \Drupal::service('account_switcher')->switchBack();
    }

    // Switch language back.
    if (!empty($this->original_language)) {
      $GLOBALS['language'] = $this->original_language;
      $GLOBALS['language_url'] = $this->original_language;
      if (\Drupal::moduleHandler()->moduleExists('i18n_select')) {
        $GLOBALS['language_content'] = $this->original_language;
      }
    }
  }

  /**
   * Build the entity object.
   *
   * The resulting build array is cached as it is used in multiple places.
   *
   * @param string|null $format
   *   (Optional) Override the default format. Defaults to getFormat().
   */
  protected function build($format = NULL) {
    if (empty($format)) {
      $format = $this->getFormat();
    }
    if (!empty($this->build[$format])) {
      return $this->build[$format];
    }

    // Build message body
    // Supported view modes: 'email_plain', 'email_html', 'email_textalt'
    $build = \Drupal::entityManager()->getViewBuilder($this->getEntity()->getEntityTypeId())->view($this->getEntity(), 'email_' . $format, $this->getLanguage());
    $build['#entity_type'] = $this->getEntity()->getEntityTypeId();
    // @todo: Consider using render caching.
    unset($build['#cache']);

    // We need to prevent the standard theming hooks, but we do want to allow
    // modules such as panelizer that override it, so only clear the standard
    // entity hook and entity type hooks.
    if ($build['#theme'] == 'entity' || $build['#theme'] == $this->getEntity()->getEntityTypeId()) {
      unset($build['#theme']);
    }

    $this->build[$format] = $build;
    return $this->build[$format];
  }

  /**
   * Build the themed newsletter body.
   *
   * @param string|null $format
   *   (Optional) Override the default format. Defaults to getFormat().
   *
   * @return string
   *   The newsletter body.
   */
  protected function buildBody($format = NULL) {
    if (empty($format)) {
      $format = $this->getFormat();
    }
    if ($cache = $this->cache->get($this, 'build', 'body:' . $format)) {
      return $cache;
    }
    $body = $this->build($format) + array(
      '#theme' => 'simplenews_newsletter_body',
      '#newsletter' => $this->getNewsletter(),
      '#language' => $this->getLanguage(),
      '#simplenews_subscriber' => $this->getSubscriber(),
      '#key' => $this->getKey(),
      '#format' => $format,
    );
    $markup = \Drupal::service('renderer')->renderPlain($body);
    $this->cache->set($this, 'build', 'body:' . $format, $markup);
    return $markup;
  }

  /**
   * {@inheritdoc}
   */
  public function getBody() {
    return $this->getBodyWithFormat($this->getFormat());
  }

  /**
   * {@inheritdoc}
   */
  public function getPlainBody() {
    return $this->getBodyWithFormat('plain');
  }

  /**
   * Get the body with the requested format.
   *
   * @param string $format
   *   Either html or plain.
   *
   * @return string
   *   The rendered mail body as a string.
   */
  protected function getBodyWithFormat($format) {
    // Switch to correct user and language context.
    $this->setContext();

    if ($cache = $this->cache->get($this, 'final', 'body:' . $format)) {
      return $cache;
    }

    $body = $this->buildBody($format);

    // Build message body, replace tokens.
    $body = \Drupal::token()->replace($body, $this->getTokenContext(), array('langcode' => $this->getLanguage()));
    if ($format == 'plain') {
      // Convert HTML to text if requested to do so.
      $body = MailFormatHelper::htmlToText($body, $this->getNewsletter()->hyperlinks);
    }
    else {
      $body = Markup::create($body);
    }
    $this->cache->set($this, 'final', 'body:' . $format, $body);
    $this->resetContext();
    return $body;
  }

  /**
   * {@inhertidoc}
   */
  function getAttachments() {
    if ($cache = $this->cache->get($this, 'data', 'attachments')) {
      return $cache;
    }

    $attachments = array();
    $build = $this->build();
    $fids = array();
    foreach ($this->getEntity()->getFieldDefinitions() as $field_name => $field_definition) {
      // @todo: Find a better way to support more field types.
      // Only add fields of type file which are enabled for the current view
      // mode as attachments.
      if ($field_definition->getType() == 'file' && isset($build[$field_name])) {

        if ($items = $this->getEntity()->get($field_name)) {
          foreach ($items as $item) {
            $fids[] = $item->target_id;
          }
        }
      }
    }
    if (!empty($fids)) {
      $attachments = File::loadMultiple($fids);
    }

    $this->cache->set($this, 'data', 'attachments', $attachments);
    return $attachments;
  }

}
