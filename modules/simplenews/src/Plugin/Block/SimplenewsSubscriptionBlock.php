<?php

namespace Drupal\simplenews\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\simplenews\Entity\Subscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Simplenews subscription' block with all available newsletters and an email field.
 *
 * @Block(
 *   id = "simplenews_subscription_block",
 *   admin_label = @Translation("Simplenews subscription"),
 *   category = @Translation("Simplenews")
 * )
 */
class SimplenewsSubscriptionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs an SimplenewsSubscriptionBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, FormBuilderInterface $formBuilder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->formBuilder = $formBuilder;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('form_builder')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // By default, the block will contain 1 newsletter.
    return array(
      'newsletters' => array(),
      'message' => t('Stay informed - subscribe to our newsletter.'),
      'unique_id' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // Only grant access to users with the 'subscribe to newsletters' permission.
    return AccessResult::allowedIfHasPermission($account, 'subscribe to newsletters');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $newsletters = simplenews_newsletter_get_visible();
    foreach ($newsletters as $newsletter) {
      $options[$newsletter->id()] = $newsletter->name;
    }

    $form['newsletters'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Newsletters'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $this->configuration['newsletters'],
    );
    $form['message'] = array(
      '#type' => 'textfield',
      '#title' => t('Block message'),
      '#size' => 60,
      '#maxlength' => 255,
      '#default_value' => $this->configuration['message'],
    );
    $form['unique_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Unique ID'),
      '#size' => 60,
      '#maxlength' => 255,
      '#description' => t('Each subscription block must have a unique form ID. If no value is provided, a random ID will be generated. Use this to have a predictable, short ID, e.g. to configure this form use a CAPTCHA.'),
      '#default_value' => $this->configuration['unique_id'],
    );
    /*if (\Drupal::moduleHandler()->moduleExists('views')) {
        $form['link_previous'] = array(
          '#type' => 'checkbox',
          '#title' => t('Display link to previous issues'),
          '#return_value' => 1,
          '#default_value' => variable_get('simplenews_block_l_' . $delta, 1),
          '#description' => t('Link points to newsletter/newsletter_id, which is provided by the newsletter issue list default view.'),
        );
      }*/
    /*if (\Drupal::moduleHandler()->moduleExists('views')) {
      $form['rss_feed'] = array(
        '#type' => 'checkbox',
        '#title' => t('Display RSS-feed icon'),
        '#return_value' => 1,
        '#default_value' => variable_get('simplenews_block_r_' . $delta, 1),
        '#description' => t('Link points to newsletter/feed/newsletter_id, which is provided by the newsletter issue list default view.'),
      );
    }*/
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['newsletters'] = array_filter($form_state->getValue('newsletters'));
    $this->configuration['message'] = $form_state->getValue('message');
    //$this->configuration['link_previous'] = $form_state->getValue('link_previous');
    //$this->configuration['rss_feed'] = $form_state->getValue('rss_feed');
    $this->configuration['unique_id'] = empty($form_state->getValue('unique_id')) ? \Drupal::service('uuid')->generate() : $form_state->getValue('unique_id');
}

  /**
   * {@inheritdoc}
   */
  public function build() {
    /** @var \Drupal\simplenews\Form\SubscriptionsBlockForm $form_object */
    $form_object = $this->entityTypeManager->getFormObject('simplenews_subscriber', 'block');
    $form_object->setUniqueId($this->configuration['unique_id']);
    $form_object->setNewsletterIds($this->configuration['newsletters']);
    $form_object->message = $this->configuration['message'];

    // Set the entity on the form.
    if ($user = \Drupal::currentUser()) {
      if ($subscriber = simplenews_subscriber_load_by_uid($user->id())) {
        $form_object->setEntity($subscriber);
      }
      else {
        $form_object->setEntity(Subscriber::create()->fillFromAccount($user));
      }
    }
    else {
      $form_object->setEntity(Subscriber::create());
    }

    return $this->formBuilder->getForm($form_object);
  }

}
