<?php

/**
 * @file
 * Simplenews source test functions.
 *
 * @ingroup simplenews
 */

namespace Drupal\simplenews\Tests;

use Drupal\Component\Utility\Unicode;
use Drupal\Component\Utility\Html;
use Drupal\node\Entity\Node;
use Drupal\simplenews\Mail\MailTest;
use Drupal\simplenews\Spool\SpoolStorageInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Test cases for creating and sending newsletters.
 *
 * @group simplenews
 */
class SimplenewsSourceTest extends SimplenewsTestBase {

  function setUp() {
    parent::setUp();

    // Create the filtered_html text format.
    $filtered_html_format = entity_create('filter_format', array(
      'format' => 'filtered_html',
      'name' => 'Filtered HTML',
      'weight' => 0,
      'filters' => array(
        // URL filter.
        'filter_url' => array(
          'weight' => 0,
          'status' => 1,
        ),
        // HTML filter.
        'filter_html' => array(
          'weight' => 1,
          'status' => 1,
          'allowed-values'
        ),
        // Line break filter.
        'filter_autop' => array(
          'weight' => 2,
          'status' => 1,
        ),
        // HTML corrector filter.
        'filter_htmlcorrector' => array(
          'weight' => 10,
          'status' => 1,
        ),
      ),
    ));
    $filtered_html_format->save();

    $admin_user = $this->drupalCreateUser(array(
      'administer newsletters',
      'send newsletter',
      'administer nodes',
      'administer simplenews subscriptions',
      'create simplenews_issue content',
      'edit any simplenews_issue content',
      'view own unpublished content',
      'delete any simplenews_issue content',
      'administer simplenews settings',
      $filtered_html_format->getPermissionName()));
    $this->drupalLogin($admin_user);
  }

  /**
   * Tests that sending a minimal implementation of the source interface works.
   */
  function testSendMinimalSourceImplementation() {

    // Create a basic plaintext test source and send it.
    $plain_mail = new MailTest('plain');
    \Drupal::service('simplenews.mailer')->sendMail($plain_mail);
    $mails = $this->drupalGetMails();
    $mail = $mails[0];

    // Assert resulting mail.
    $this->assertEqual('simplenews_node', $mail['id']);
    $this->assertEqual('simplenews', $mail['module']);
    $this->assertEqual('node', $mail['key']);
    $this->assertEqual($plain_mail->getRecipient(), $mail['to']);
    $this->assertEqual($plain_mail->getFromAddress(), $mail['from']);
    $this->assertEqual($plain_mail->getFromFormatted(), $mail['reply-to']);
    $this->assertEqual($plain_mail->getLanguage(), $mail['langcode']);
    $this->assertTrue($mail['params']['plain']);

    $this->assertFalse(isset($mail['params']['plaintext']));
    $this->assertFalse(isset($mail['params']['attachments']));

    $this->assertEqual($plain_mail->getSubject(), $mail['subject']);
    $this->assertTrue(strpos($mail['body'], 'the plain body') !== FALSE);

    $html_mail = new MailTest('html');
    \Drupal::service('simplenews.mailer')->sendMail($html_mail);
    $mails = $this->drupalGetMails();
    $mail = $mails[1];

    // Assert resulting mail.
    $this->assertEqual('simplenews_node', $mail['id']);
    $this->assertEqual('simplenews', $mail['module']);
    $this->assertEqual('node', $mail['key']);
    $this->assertEqual($plain_mail->getRecipient(), $mail['to']);
    $this->assertEqual($plain_mail->getFromAddress(), $mail['from']);
    $this->assertEqual($plain_mail->getFromFormatted(), $mail['reply-to']);
    $this->assertEqual($plain_mail->getLanguage(), $mail['langcode']);
    $this->assertEqual(NULL, $mail['params']['plain']);

    $this->assertTrue(isset($mail['params']['plaintext']));
    $this->assertTrue(strpos($mail['params']['plaintext'], 'the plain body') !== FALSE);
    $this->assertTrue(isset($mail['params']['attachments']));
    $this->assertEqual('example://test.png', $mail['params']['attachments'][0]['uri']);

    $this->assertEqual($plain_mail->getSubject(), $mail['subject']);
    $this->assertTrue(strpos($mail['body'], 'the body') !== FALSE);
  }

  /**
   * Test sending a newsletter to 100 recipients with caching enabled.
   */
  function testSendCaching() {

    $this->setUpSubscribers(100);

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'body[0][value]' => "Mail token: <strong>[simplenews-subscriber:mail]</strong>",
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    // Add node to spool.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    // Unsubscribe one of the recipients to make sure that he doesn't receive
    // the mail.
    \Drupal::service('simplenews.subscription_manager')->unsubscribe(array_shift($this->subscribers), $this->getRandomNewsletter(), FALSE, 'test');

    $before = microtime(TRUE);
    \Drupal::service('simplenews.mailer')->sendSpool();
    $after = microtime(TRUE);

    // Make sure that 99 mails have been sent.
    $this->assertEqual(99, count($this->drupalGetMails()));

    // Test that tokens are correctly replaced.
    foreach (array_slice($this->drupalGetMails(), 0, 3) as $mail) {
      // Make sure that the same mail was used in the body token as it has been
      // sent to. Also verify that the mail is plaintext.
      $this->assertTrue(strpos($mail['body'], '*' . $mail['to'] . '*') !== FALSE);
      $this->assertFalse(strpos($mail['body'], '<strong>'));
      // Make sure the body is only attached once.
      $this->assertEqual(1, preg_match_all('/Mail token/', $mail['body'], $matches));

      $this->assertTrue(strpos($mail['body'], (string) t('Unsubscribe from this newsletter')));
      // Make sure the mail has the correct unsubscribe hash.
      $hash = simplenews_generate_hash($mail['to'], 'remove');
      $this->assertTrue(strpos($mail['body'], $hash), 'Correct hash is used');
      $this->assertTrue(strpos($mail['headers']['List-Unsubscribe'], $hash), 'Correct hash is used in header');
    }

    // Report time. @todo: Find a way to actually do some assertions here.
    $this->pass(t('Mails have been sent in @sec seconds with build caching enabled.', array('@sec' => round($after - $before, 3))));
  }

  /**
   * Send a newsletter with the HTML format.
   */
  function testSendHTML() {
    $this->setUpSubscribers(5);

    // Use custom testing mail system to support HTML mails.
    $mail_config = $this->config('system.mail');
    $mail_config->set('interface.default', 'test_simplenews_html_mail');
    $mail_config->save();

    // Set the format to HTML.
    $this->drupalGet('admin/config/services/simplenews');
    $this->clickLink(t('Edit'));
    $edit_newsletter = array(
      'format' => 'html',
      // Use umlaut to provoke mime encoding.
      'from_name' => 'Drupäl',
      // @todo: This shouldn't be necessary, default value is missing. Probably
      // should not be required.
      'from_address' => $this->randomEmail(),
      // Request a confirmation receipt.
      'receipt' => TRUE,
    );
    $this->drupalPostForm(NULL, $edit_newsletter, t('Save'));
    $this->clickLink(t('Edit'));

    $edit = array(
      // Always use a character that is escaped.
      'title[0][value]' => $this->randomString() . '\'<',
      'body[0][value]' => "Mail token: <strong>[simplenews-subscriber:mail]</strong>",
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    // Add node to spool.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    // Send mails.
    \Drupal::service('simplenews.mailer')->sendSpool();

    // Make sure that 5 mails have been sent.
    $this->assertEqual(5, count($this->drupalGetMails()));

    // Test that tokens are correctly replaced.
    foreach (array_slice($this->drupalGetMails(), 0, 3) as $mail) {
      // Verify title.
      $this->assertTrue(strpos($mail['body'], '<h2>' . Html::escape($node->getTitle()) . '</h2>') !== FALSE);

      // Verify the format/content type.
      $this->assertEqual($mail['params']['format'], 'text/html');
      $this->assertEqual($mail['params']['plain'], NULL);
      $this->assertEqual($mail['headers']['Content-Type'], 'text/html; charset=UTF-8');

      // Make sure that the same mail was used in the body token as it has been
      // sent to.
      $this->assertTrue(strpos($mail['body'], '<strong>' . $mail['to'] . '</strong>') !== FALSE);

      // Make sure the body is only attached once.
      $this->assertEqual(1, preg_match_all('/Mail token/', $mail['body'], $matches));

      // Check the plaintext version, both params][plaintext (Mime Mail) and
      // plain (Swiftmailer).
      $this->assertTrue(strpos($mail['params']['plaintext'], $mail['to']) !== FALSE);
      $this->assertFalse(strpos($mail['params']['plaintext'], '<strong>'));
      $this->assertEqual($mail['params']['plaintext'], $mail['plain']);
      // Make sure the body is only attached once.
      $this->assertEqual(1, preg_match_all('/Mail token/', $mail['params']['plaintext'], $matches));

      // Check the attachments and files arrays.
      $this->assertTrue(is_array($mail['params']['attachments']));
      $this->assertEqual($mail['params']['attachments'], $mail['params']['files']);

      // Make sure formatted address is properly encoded.
      $from = '"' . addslashes(Unicode::mimeHeaderEncode($edit_newsletter['from_name'])) . '" <' . $edit_newsletter['from_address'] . '>';
      $this->assertEqual($from, $mail['reply-to']);
      // And make sure it won't get encoded twice.
      $this->assertEqual($from, Unicode::mimeHeaderEncode($mail['reply-to']));

      // @todo: Improve this check, there are currently two spaces, not sure
      // where they are coming from.
      $this->assertTrue(strpos($mail['body'], 'class="newsletter-footer"'));

      // Verify receipt headers.
      $this->assertEqual($mail['headers']['Disposition-Notification-To'], $edit_newsletter['from_address']);
      $this->assertEqual($mail['headers']['X-Confirm-Reading-To'], $edit_newsletter['from_address']);
    }
  }

  /**
   * Send a issue with the newsletter set to hidden.
   */
  function testSendHidden() {
    $this->setUpSubscribers(5);

    // Set the format to HTML.
    $this->drupalGet('admin/config/services/simplenews');
    $this->clickLink(t('Edit'));
    $edit = array(
      'opt_inout' => 'hidden',
      // @todo: This shouldn't be necessary.
      'from_address' => $this->randomEmail(),
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'body[0][value]' => "Mail token: <strong>[simplenews-subscriber:mail]</strong>",
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    // Add node to spool.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    // Send mails.
    \Drupal::service('simplenews.mailer')->sendSpool();

    // Make sure that 5 mails have been sent.
    $this->assertEqual(5, count($this->drupalGetMails()));

    // Test that tokens are correctly replaced.
    foreach (array_slice($this->drupalGetMails(), 0, 3) as $mail) {
      // Verify the unsubscribe link is not displayed for hidden newsletters.
      $this->assertFalse(strpos($mail['body'], (string) t('Unsubscribe from this newsletter')));
    }
  }

  /**
   * Test with disabled caching.
   */
  function testSendNoCaching() {
    $this->setUpSubscribers(100);

    // Disable caching.
    $yaml = new Yaml();
    $directory = DRUPAL_ROOT . '/' . $this->siteDirectory;
    $content = file_get_contents($directory . '/services.yml');
    $services = $yaml->parse($content);
    $services['services']['simplenews.mail_cache'] = [
      'class' => 'Drupal\simplenews\Mail\MailCacheNone',
    ];
    file_put_contents($directory . '/services.yml', $yaml->dump($services));
    $this->rebuildContainer();
    \Drupal::moduleHandler()->loadAll();

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'body[0][value]' => "Mail token: <strong>[simplenews-subscriber:mail]</strong>",
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    // Add node to spool.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);

    $before = microtime(TRUE);
    \Drupal::service('simplenews.mailer')->sendSpool();
    $after = microtime(TRUE);

    // Make sure that 100 mails have been sent.
    $this->assertEqual(100, count($this->drupalGetMails()));

    // Test that tokens are correctly replaced.
    foreach (array_slice($this->drupalGetMails(), 0, 3) as $mail) {
      // Make sure that the same mail was used in the body token as it has been
      // sent to. Also verify that the mail is plaintext.
      $this->assertTrue(strpos($mail['body'], '*' . $mail['to'] . '*') !== FALSE);
      $this->assertFalse(strpos($mail['body'], '<strong>'));
      // Make sure the body is only attached once.
      $this->assertEqual(1, preg_match_all('/Mail token/', $mail['body'], $matches));
    }

    // Report time. @todo: Find a way to actually do some assertions here.
    $this->pass(t('Mails have been sent in @sec seconds with caching disabled.', array('@sec' => round($after - $before, 3))));
  }

  /**
   * Test sending when the issue node is missing.
   */
  function testSendMissingNode() {
    $this->setUpSubscribers(1);

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'body[0][value]' => "Mail token: <strong>[simplenews-subscriber:mail]</strong>",
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    // Add node to spool.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);

    // Delete the node manually in the database.
    db_delete('node')
      ->condition('nid', $node->id())
      ->execute();
    db_delete('node_revision')
      ->condition('nid', $node->id())
      ->execute();
    \Drupal::entityManager()->getStorage('node')->resetCache();

    \Drupal::service('simplenews.mailer')->sendSpool();

    // Make sure that no mails have been sent.
    $this->assertEqual(0, count($this->drupalGetMails()));

    $spool_row = db_query('SELECT * FROM {simplenews_mail_spool}')->fetchObject();
    $this->assertEqual(SpoolStorageInterface::STATUS_DONE, $spool_row->status);
  }

  /**
   * Test sending when there are no subscribers.
   */
  function testSendMissingSubscriber() {
    $this->setUpSubscribers(1);

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'body[0][value]' => "Mail token: <strong>[simplenews-subscriber:mail]</strong>",
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews_issue', $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    // Add node to spool.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);

    // Delete the subscriber.
    $subscriber = simplenews_subscriber_load_by_mail(reset($this->subscribers));
    $subscriber->delete();

    \Drupal::service('simplenews.mailer')->sendSpool();

    // Make sure that no mails have been sent.
    $this->assertEqual(0, count($this->drupalGetMails()));

    $spool_row = db_query('SELECT * FROM {simplenews_mail_spool}')->fetchObject();
    $this->assertEqual(SpoolStorageInterface::STATUS_DONE, $spool_row->status);
  }

  /**
   * Test handling of the skip exception.
   */
  public function testSkip() {
    $this->setUpSubscribers(1);
    // Setting the body to "Nothing interesting" provokes an exception in
    // simplenews_test_mail_alter().
    $node = $this->drupalCreateNode([
      'body' => 'Nothing interesting',
      'type' => 'simplenews_issue',
      'simplenews_issue' => ['target_id' => 'default'],
    ]);
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    \Drupal::service('simplenews.mailer')->sendSpool();
    $this->assertEqual(0, count($this->drupalGetMails()));
    $spool_row = db_select('simplenews_mail_spool', 'ms')
      ->fields('ms', ['status'])
      ->execute()
      ->fetchAssoc();
    $this->assertEqual(SpoolStorageInterface::STATUS_SKIPPED, $spool_row['status']);
  }
}
