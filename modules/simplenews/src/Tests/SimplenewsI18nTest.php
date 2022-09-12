<?php

namespace Drupal\simplenews\Tests;

use Drupal\Component\Utility\Unicode;
use Drupal\field\Entity\FieldConfig;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\node\Entity\Node;
use Drupal\simplenews\Entity\Newsletter;

/**
 * Translation of newsletters and issues.
 *
 * @group simplenews
 */
class SimplenewsI18nTest extends SimplenewsTestBase {

  /**
   * Modules to enable.
   *
   * @var  array
   */
  public static $modules = ['locale', 'config_translation', 'content_translation'];

  /**
   * Administrative user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Default language.
   *
   * @var string
   */
  protected $defaultLanguage;

  /**
   * Secondary language.
   *
   * @var string
   */
  protected $secondaryLanguage;

  function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser(array('bypass node access', 'administer nodes', 'administer languages', 'administer content types', 'access administration pages', 'administer filters', 'translate interface', 'subscribe to newsletters', 'administer site configuration', 'translate any entity', 'administer content translation', 'administer simplenews subscriptions', 'send newsletter', 'create content translations'));
    $this->drupalLogin($this->adminUser);
    $this->setUpLanguages();
  }

  /**
   * Set up configuration for multiple languages.
   */
  function setUpLanguages() {

    // Add languages.
    $this->defaultLanguage = 'en';
    $this->secondaryLanguage = 'es';
    $this->addLanguage($this->secondaryLanguage);

    // Display the language widget.
    $config = ContentLanguageSettings::loadByEntityTypeBundle('node', 'simplenews_issue');
    $config->setLanguageAlterable(TRUE);
    $config->save();

    // Make Simplenews issue translatable.
    \Drupal::service('content_translation.manager')->setEnabled('node', 'simplenews_issue', TRUE);
    drupal_static_reset();
    \Drupal::entityManager()->clearCachedDefinitions();
    \Drupal::service('router.builder')->rebuild();
    \Drupal::service('entity.definition_update_manager')->applyUpdates();

    // Make Simplenews issue body translatable.
    $field = FieldConfig::loadByName('node', 'simplenews_issue', 'body');
    $field->setTranslatable(TRUE);
    $field->save();
    $this->rebuildContainer();
  }

  /**
   * Install a the specified language if it has not been already. Otherwise make sure that
   * the language is enabled.
   *
   * Copied from Drupali18nTestCase::addLanguage().
   *
   * @param $language_code
   *   The language code the check.
   */
  function addLanguage($language_code) {
    $language = ConfigurableLanguage::createFromLangcode($language_code);
    $language->save();
  }

  function testNewsletterIssueTranslation() {
    // Sign up three users, one in english and two in spanish.
    $english_mail = $this->randomEmail();
    $spanish_mail = $this->randomEmail();
    $spanish_mail2 = $this->randomEmail();
    $newsletter_id = $this->getRandomNewsletter();

    /** @var \Drupal\simplenews\Subscription\SubscriptionManagerInterface $subscription_manager */
    $subscription_manager = \Drupal::service('simplenews.subscription_manager');

    $subscription_manager->subscribe($english_mail, $newsletter_id, FALSE, 'english', 'en');
    $subscription_manager->subscribe($spanish_mail, $newsletter_id, FALSE, 'spanish', 'es');
    $subscription_manager->subscribe($spanish_mail2, $newsletter_id, FALSE, 'spanish', 'es');

    // Enable translation for newsletters.
    $edit = array(
      'language_configuration[content_translation]' => TRUE,
    );
    $this->drupalPostForm('admin/structure/types/manage/simplenews_issue', $edit, t('Save content type'));

    // Create a Newsletter including a translation.
    $newsletter_id = $this->getRandomNewsletter();
    $english = array(
      'title[0][value]' => $this->randomMachineName(),
      'simplenews_issue' => $newsletter_id,
      'body[0][value]' => 'Link to node: [node:url]',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $english, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    $this->clickLink(t('Translate'));
    $this->clickLink(t('Add'));
    $spanish = array(
      'title[0][value]' => $this->randomMachineName(),
      'body[0][value]' => 'Link to node: [node:url] ES',
    );
    $this->drupalPostForm(NULL, $spanish, t('Save and keep published (this translation)'));

    \Drupal::entityManager()->getStorage('node')->resetCache(array($node->id()));
    $node = Node::load($node->id());
    $translation = $node->getTranslation($this->secondaryLanguage);

    // Send newsletter.
    $this->clickLink(t('Newsletter'));
    $this->drupalPostForm(NULL, array(), t('Send now'));
    $this->cronRun();
    //simplenews_cron();

    $this->assertEqual(3, count($this->drupalGetMails()));

    $newsletter = Newsletter::load($newsletter_id);
    foreach ($this->drupalGetMails() as $mail) {

      if ($mail['to'] == $english_mail) {
        $this->assertEqual('en', $mail['langcode']);
        $this->assertEqual('[' . $newsletter->label() . '] ' . $node->getTitle(), $mail['subject']);
        $node_url = $node->url('canonical', ['absolute' => TRUE]);
        $title = $node->getTitle();
      }
      elseif ($mail['to'] == $spanish_mail || $mail['to'] == $spanish_mail2) {
        $this->assertEqual('es', $mail['langcode']);
        // @todo: Verify newsletter translation once supported again.
        $this->assertEqual('[' . $newsletter->name . '] ' . $translation->label(), $mail['subject']);
        $node_url = $translation->url('canonical', ['absolute' => TRUE, 'language' => $translation->language()]);
        $title = $translation->getTitle();
      }
      else {
        $this->fail(t('Mail not sent to expected recipient'));
      }

      // Verify that the link is in the correct language.
      $this->assertTrue(strpos($mail['body'], $node_url) !== FALSE);
      // The <h1> tag is converted to uppercase characters.
      $this->assertTrue(strpos($mail['body'], Unicode::strtoupper($title)) !== FALSE);
    }

    // Verify sent subscriber count for each node.
    \Drupal::entityManager()->getStorage('node')->resetCache(array($node->id()));
    $node = Node::load($node->id());
    $translation = $node->getTranslation($this->secondaryLanguage);
    $this->assertEqual(1, $node->simplenews_issue->sent_count, 'subscriber count is correct for english');
    $this->assertEqual(2, $translation->simplenews_issue->sent_count, 'subscriber count is correct for spanish');

    // Make sure the language of a node can be changed.
    $english = array(
      'title[0][value]' => $this->randomMachineName(),
      'langcode[0][value]' => 'en',
      'body[0][value]' => 'Link to node: [node:url]',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $english, ('Save and publish'));
    $this->clickLink(t('Edit'));
    $edit = array(
      'langcode[0][value]' => 'es',
    );
    $this->drupalPostForm(NULL, $edit, t('Save and keep published'));
  }

}
