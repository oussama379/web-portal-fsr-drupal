<?php

/**
 * @file
 * Simplenews send test functions.
 *
 * @ingroup simplenews
 */

namespace Drupal\simplenews\Tests;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Test cases for creating and sending newsletters.
 *
 * @group simplenews
 */
class SimplenewsSendTest extends SimplenewsTestBase {

  function setUp() {
    parent::setUp();

    $admin_user = $this->drupalCreateUser(array(
      'administer newsletters',
      'send newsletter',
      'administer nodes',
      'administer simplenews subscriptions',
      'create simplenews_issue content',
      'edit any simplenews_issue content',
      'view own unpublished content',
      'delete any simplenews_issue content',
    ));
    $this->drupalLogin($admin_user);

    // Subscribe a few users.
    $this->setUpSubscribers(5);
  }

  /**
   * Creates and sends a node using the API.
   */
  function testProgrammaticNewsletter() {
    // Create a very basic node.
    $node = Node::create(array(
      'type' => 'simplenews_issue',
      'title' => $this->randomString(10),
      'uid' => 0,
      'status' => 1
    ));
    $node->simplenews_issue->target_id = $this->getRandomNewsletter();
    $node->simplenews_issue->handler = 'simplenews_all';
    $node->save();

    // Send the node.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    $node->save();

    // Send mails.
    \Drupal::service('simplenews.mailer')->sendSpool();
    \Drupal::service('simplenews.spool_storage')->clear();
    // Update sent status for newsletter admin panel.
    \Drupal::service('simplenews.mailer')->updateSendStatus();

    // Verify mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('All mails were sent.'));
    foreach ($mails as $mail) {
      $this->assertEqual($mail['subject'], '[Default newsletter] ' . $node->getTitle(), t('Mail has correct subject'));
      $this->assertTrue(isset($this->subscribers[$mail['to']]), t('Found valid recipient'));
      unset($this->subscribers[$mail['to']]);
    }
    $this->assertEqual(0, count($this->subscribers), t('all subscribers have been received a mail'));

    // Create another node.
    $node = Node::create(array(
      'type' => 'simplenews_issue',
      'title' => $this->randomString(10),
      'uid' => 0,
      'status' => 1
    ));
    $node->simplenews_issue->target_id = $this->getRandomNewsletter();
    $node->simplenews_issue->handler = 'simplenews_all';
    $node->save();

    // Send the node.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    $node->save();

    // Make sure that they have been added.
    $this->assertEqual(\Drupal::service('simplenews.spool_storage')->countMails(), 5);

    // Mark them as pending, fake a currently running send process.
    $this->assertEqual(count(\Drupal::service('simplenews.spool_storage')->getMails(2)), 2);

    // Those two should be excluded from the count now.
    $this->assertEqual(\Drupal::service('simplenews.spool_storage')->countMails(), 3);

    // Get two additional spool entries.
    $this->assertEqual(count(\Drupal::service('simplenews.spool_storage')->getMails(2)), 2);

    // Now only one should be returned by the count.
    $this->assertEqual(\Drupal::service('simplenews.spool_storage')->countMails(), 1);
  }

  /**
   * Send a newsletter using cron.
   */
  function testSendNowNoCron() {
    // Disable cron.
    $config = $this->config('simplenews.settings');
    $config->set('mail.use_cron', FALSE);
    $config->save();

    // Verify that the newsletter settings are shown.
    $this->drupalGet('node/add/simplenews_issue');
    $this->assertText(t('Create Newsletter Issue'));

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm(NULL, $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    $this->clickLink(t('Newsletter'));
    $this->assertText(t('Send'));
    $this->assertText(t('Test'));
    $this->assertNoText(t('Send newsletter when published'), t('Send on publish is not shown for published nodes.'));

    // Verify state.
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter not sent yet.'));

    // Send now.
    $this->drupalPostForm(NULL, array(), t('Send now'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_READY, $node->simplenews_issue->status, t('Newsletter sending finished'));

    // Verify mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('All mails were sent.'));
    foreach ($mails as $mail) {
      $this->assertEqual($mail['subject'], '[Default newsletter] ' . $edit['title[0][value]'], t('Mail has correct subject'));
      $this->assertTrue(isset($this->subscribers[$mail['to']]), t('Found valid recipient'));
      unset($this->subscribers[$mail['to']]);
    }
    $this->assertEqual(0, count($this->subscribers), t('all subscribers have been received a mail'));

    $this->assertEqual(5, $node->simplenews_issue->sent_count, 'subscriber count is correct');
  }

  /**
   * Send a newsletter using cron.
   */
  function testSendMultipleNoCron() {
    // Disable cron.
    $config = $this->config('simplenews.settings');
    $config->set('mail.use_cron', FALSE);
    $config->save();

    // Verify that the newsletter settings are shown.
    $nodes = array();
    for ($i = 0; $i < 3; $i++) {
      $this->drupalGet('node/add/simplenews_issue');
      $this->assertText(t('Create Newsletter Issue'));

      $edit = array(
        'title[0][value]' => $this->randomString(10),
        'simplenews_issue' => 'default',
      );
      // The last newsletter shouldn't be published.
      if ($i != 2) {
        $this->drupalPostForm(NULL, $edit, ('Save and publish'));
      }
      else {
        $this->drupalPostForm(NULL, $edit, ('Save as unpublished'));
      }
      $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
      $nodes[] = Node::load($matches[1]);

      // Verify state.
      $node = current($nodes);
      $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter not sent yet.'));
    }
  }

  /**
   * Send a newsletter using cron and a low throttle.
   */
  function testSendNowCronThrottle() {
    $config = $this->config('simplenews.settings');
    $config->set('mail.throttle', 3);
    $config->save();

    // Verify that the newsletter settings are shown.
    $this->drupalGet('node/add/simplenews_issue');
    $this->assertText(t('Create Newsletter Issue'));

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm(NULL, $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    $this->clickLink(t('Newsletter'));
    $this->assertText(t('Send'));
    $this->assertText(t('Test'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter not sent yet.'));

    // Send now.
    $this->drupalPostForm(NULL, array(), t('Send now'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_PENDING, $node->simplenews_issue->status, t('Newsletter sending pending.'));

    // Verify that no mails have been sent yet.
    $mails = $this->drupalGetMails();
    $this->assertEqual(0, count($mails), t('No mails were sent yet.'));

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(5, $spooled, t('5 mails have been added to the mail spool'));

    // Run cron for the first time.
    simplenews_cron();

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_PENDING, $node->simplenews_issue->status, t('Newsletter sending pending.'));
    $this->assertEqual(3, $node->simplenews_issue->sent_count, 'subscriber count is correct');

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(2, $spooled, t('2 mails remaining in spool.'));

    // Run cron for the second time.
    simplenews_cron();

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_READY, $node->simplenews_issue->status, t('Newsletter sending finished.'));

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(0, $spooled, t('No mails remaining in spool.'));

    // Verify mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('All mails were sent.'));
    foreach ($mails as $mail) {
      $this->assertEqual($mail['subject'], '[Default newsletter] ' . $edit['title[0][value]'], t('Mail has correct subject'));
      $this->assertTrue(isset($this->subscribers[$mail['to']]), t('Found valid recipient'));
      unset($this->subscribers[$mail['to']]);
    }
    $this->assertEqual(0, count($this->subscribers), t('all subscribers have been received a mail'));
    $this->assertEqual(5, $node->simplenews_issue->sent_count);
  }

  /**
   * Send a newsletter without using cron.
   */
  function testSendNowCron() {

    // Verify that the newsletter settings are shown.
    $this->drupalGet('node/add/simplenews_issue');
    $this->assertText(t('Create Newsletter Issue'));

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'simplenews_issue' => 'default',
    );
    // Try preview first.
    $this->drupalPostForm(NULL, $edit, t('Preview'));

    $this->clickLink(t('Back to content editing'));

    // Then save.
    $this->drupalPostForm(NULL, array(), t('Save and publish'));

    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    $this->clickLink(t('Newsletter'));
    $this->assertText(t('Send'));
    $this->assertText(t('Test'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter not sent yet.'));

    // Send now.
    $this->drupalPostForm(NULL, array(), t('Send now'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_PENDING, $node->simplenews_issue->status, t('Newsletter sending pending.'));

    // Verify that no mails have been sent yet.
    $mails = $this->drupalGetMails();
    $this->assertEqual(0, count($mails), t('No mails were sent yet.'));

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(5, $spooled, t('5 mails have been added to the mail spool'));

    // Check warning message on node edit form.
    $this->clickLink(t('Edit'));
    $this->assertText(t('This newsletter issue is currently being sent. Any changes will be reflected in the e-mails which have not been sent yet.'));

    // Run cron.
    simplenews_cron();

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_READY, $node->simplenews_issue->status, t('Newsletter sending finished.'));

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(0, $spooled, t('No mails remaining in spool.'));

    // Verify mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('All mails were sent.'));
    foreach ($mails as $mail) {
      // @todo Temporarily strip tags from mail subjects until
      //   https://www.drupal.org/node/2575791 is fixed.
      $this->assertEqual($mail['subject'], '[Default newsletter] ' . strip_tags($edit['title[0][value]']), t('Mail has correct subject'));
      $this->assertTrue(isset($this->subscribers[$mail['to']]), t('Found valid recipient'));
      unset($this->subscribers[$mail['to']]);
    }
    $this->assertEqual(0, count($this->subscribers), t('all subscribers have been received a mail'));
  }

  /**
   * Send a newsletter on publish without using cron.
   */
  function testSendPublishNoCron() {
    // Disable cron.
    $config = $this->config('simplenews.settings');
    $config->set('mail.use_cron', FALSE);
    $config->save();

    // Verify that the newsletter settings are shown.
    $this->drupalGet('node/add/simplenews_issue');
    $this->assertText(t('Create Newsletter Issue'));

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm(NULL, $edit, ('Save as unpublished'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    $this->clickLink(t('Newsletter'));
    $this->assertText(t('Send'));
    $this->assertText(t('Test'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter not sent yet.'));

    // Send now.
    $this->drupalPostForm(NULL, array(), t('Send on publish'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache(array($node->id()));
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_PUBLISH, $node->simplenews_issue->status, t('Newsletter set up for sending on publish.'));

    $this->clickLink(t('Edit'));
    $this->drupalPostForm(NULL, array(), t('Save and publish'));

    // Send on publish does not send immediately.
    \Drupal::entityManager()->getStorage('node')->resetCache(array($node->id()));
    \Drupal::service('simplenews.mailer')->attemptImmediateSend(array(), FALSE);

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache(array($node->id()));
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_READY, $node->simplenews_issue->status, t('Newsletter sending finished'));
    // @todo test sent subscriber count.
    // Verify mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('All mails were sent.'));
    foreach ($mails as $mail) {
      $this->assertEqual($mail['subject'], '[Default newsletter] ' . $edit['title[0][value]'], t('Mail has correct subject'));
      $this->assertTrue(isset($this->subscribers[$mail['to']]), t('Found valid recipient'));
      unset($this->subscribers[$mail['to']]);
    }
    $this->assertEqual(0, count($this->subscribers), t('all subscribers have been received a mail'));
  }

  function testUpdateNewsletter() {
    // Create a second newsletter.
    $this->drupalGet('admin/config/services/simplenews');
    $this->clickLink(t('Add newsletter'));
    $edit = array(
      'name' => $this->randomString(10),
      'id' => strtolower($this->randomMachineName(10)),
      'description' => $this->randomString(20),
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertText(t('Newsletter @name has been added', array('@name' => $edit['name'])));

    $this->drupalGet('node/add/simplenews_issue');
    $this->assertText(t('Create Newsletter Issue'));

    $first_newsletter_id = $this->getRandomNewsletter();

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'simplenews_issue' => $first_newsletter_id,
    );
    $this->drupalPostForm(NULL, $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created.');

    // Verify newsletter.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($matches[1]);
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter sending not started.'));
    $this->assertEqual($first_newsletter_id, $node->simplenews_issue->target_id);

    do {
      $second_newsletter_id = $this->getRandomNewsletter();
    } while ($first_newsletter_id == $second_newsletter_id);


    $this->clickLink(t('Edit'));
    $update = array(
      'simplenews_issue' => $second_newsletter_id,
    );
    $this->drupalPostForm(NULL, $update, t('Save and keep published'));

    // Verify newsletter.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter sending not started.'));
    $this->assertEqual($second_newsletter_id, $node->simplenews_issue->target_id, t('Newsletter has newsletter_id ' . $second_newsletter_id . '.'));
  }

  /**
   * Create a newsletter, send mails and then delete.
   */
  function testDelete() {
    // Verify that the newsletter settings are shown.
    $this->drupalGet('node/add/simplenews_issue');
    $this->assertText(t('Create Newsletter Issue'));

    // Prevent deleting the mail spool entries automatically.
    $config = $this->config('simplenews.settings');
    $config->set('mail.spool_expire', 1);
    $config->save();

    $edit = array(
      'title[0][value]' => $this->randomString(10),
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm(NULL, $edit, ('Save and publish'));
    $this->assertTrue(preg_match('|node/(\d+)$|', $this->getUrl(), $matches), 'Node created');
    $node = Node::load($matches[1]);

    $this->clickLink(t('Newsletter'));
    $this->assertText(t('Send'));
    $this->assertText(t('Test'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_NOT, $node->simplenews_issue->status, t('Newsletter not sent yet.'));

    // Send now.
    $this->drupalPostForm(NULL, array(), t('Send now'));

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_PENDING, $node->simplenews_issue->status, t('Newsletter sending pending.'));

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(5, $spooled, t('5 mails remaining in spool.'));

    // Verify that deleting isn't possible right now.
    $this->clickLink(t('Edit'));
    $this->assertNoText(t('Delete'));

    // Send mails.
    simplenews_cron();

    // Verify state.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $node = Node::load($node->id());
    $this->assertEqual(SIMPLENEWS_STATUS_SEND_READY, $node->simplenews_issue->status, t('Newsletter sending finished'));

    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(5, $spooled, t('Mails are kept in simplenews_mail_spool after being sent.'));

    // Verify mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('All mails were sent.'));
    foreach ($mails as $mail) {
      $this->assertEqual($mail['subject'], '[Default newsletter] ' . $edit['title[0][value]'], t('Mail has correct subject'));
      $this->assertTrue(isset($this->subscribers[$mail['to']]), t('Found valid recipient'));
      unset($this->subscribers[$mail['to']]);
    }
    $this->assertEqual(0, count($this->subscribers), t('all subscribers have received a mail'));

    // Update timestamp to simulate pending lock expiration.
    db_update('simplenews_mail_spool')
      ->fields(array(
        'timestamp' => REQUEST_TIME - $this->config('simplenews.settings')->get('mail.spool_progress_expiration') - 1,
      ))
      ->execute();

    // Verify that kept mail spool rows are not re-sent.
    simplenews_cron();
    \Drupal::service('simplenews.spool_storage')->getMails();
    $mails = $this->drupalGetMails();
    $this->assertEqual(5, count($mails), t('No additional mails have been sent.'));

    // Now delete.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $this->drupalGet($node->urlInfo('edit-form'));
    $this->clickLink(t('Delete'));
    $this->drupalPostForm(NULL, array(), t('Delete'));

    // Verify.
    \Drupal::entityManager()->getStorage('node')->resetCache();
    $this->assertFalse(Node::load($node->id()));
    $spooled = db_query('SELECT COUNT(*) FROM {simplenews_mail_spool} WHERE entity_id = :nid AND entity_type = :type', array(':nid' => $node->id(), ':type' => 'node'))->fetchField();
    $this->assertEqual(0, $spooled, t('No mails remaining in spool.'));
  }

  /**
   * Test that the correct user is used when sending newsletters.
   */
  function testImpersonation() {

    // Create user to manage subscribers.
    $admin_user = $this->drupalCreateUser(array('administer users'));
    $this->drupalLogin($admin_user);

    // Add users for some existing subscribers.
    $subscribers = array_slice($this->subscribers, -3);
    $users = array();
    foreach ($subscribers as $subscriber) {
      $user = User::create(array(
        'name' => $this->randomMachineName(),
        'mail' => $subscriber,
        'status' => 1
      ));
      $user->save();
      $users[$subscriber] = $user->id();
    }

    // Create a very basic node.
    $node = Node::create(array(
      'type' => 'simplenews_issue',
      'title' => $this->randomString(10),
      'uid' => '0',
      'status' => 1,
      'body' => 'User ID: [current-user:uid]'
    ));

    $node->simplenews_issue->target_id = $this->getRandomNewsletter();
    $node->simplenews_issue->handler = 'simplenews_all';
    $node->save();

    // Send the node.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    $node->save();

    // Send mails.
    \Drupal::service('simplenews.mailer')->sendSpool();
    \Drupal::service('simplenews.spool_storage')->clear();
    // Update sent status for newsletter admin panel.
    \Drupal::service('simplenews.mailer')->updateSendStatus();

    // Verify mails.
    $mails = $this->drupalGetMails();
    // Check the mails sent to subscribers (who are also users) and verify each
    // users uid in the mail body.
    $mails_with_users = 0;
    $mails_without_users = 0;
    foreach ($mails as $mail) {
      $body = $mail['body'];
      $user_mail = $mail['to'];
      if (isset($users[$user_mail])) {
        if (strpos($body, 'User ID: ' . $users[$user_mail])) {
          $mails_with_users++;
        }
      }
      else {
        if (strpos($body, 'User ID: not yet assigned')) {
          $mails_without_users++;
        }
      }
    }
    $this->assertEqual(3, $mails_with_users, '3 mails with user ids found');
    $this->assertEqual(2, $mails_without_users, '2 mails with no user ids found');
  }

  /**
   * Test the theme suggestions when sending mails.
   */
  function testNewsletterTheme() {
    // Install and enable the test theme.
    \Drupal::service('theme_handler')->install(array('simplenews_newsletter_test_theme'));
    \Drupal::theme()->setActiveTheme(\Drupal::service('theme.initialization')->initTheme('simplenews_newsletter_test_theme'));

    $node = Node::create(array(
      'type' => 'simplenews_issue',
      'title' => $this->randomString(10),
      'uid' => '0',
      'status' => 1,
    ));
    $node->simplenews_issue->target_id = $this->getRandomNewsletter();
    $node->simplenews_issue->handler = 'simplenews_all';
    $node->save();

    // Send the node.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    $node->save();

    // Send mails.
    \Drupal::service('simplenews.mailer')->sendSpool();
    \Drupal::service('simplenews.spool_storage')->clear();
    // Update sent status for newsletter admin panel.
    \Drupal::service('simplenews.mailer')->updateSendStatus();

    $mails = $this->drupalGetMails();

    // Check if the correct theme was used in mails.
    $this->assertTrue(strpos($mails[0]['body'], 'Simplenews test theme') != FALSE);
    $this->assertTrue(preg_match('/ID: [0-9]/', $mails[0]['body']), 'Mail contains the subscriber ID');
  }

  /**
   * Test the correct handlling of HTML special characters in plain text mails.
   */
  function testHtmlEscaping() {

    $title = '><\'"-&&amp;--*';
    $node = Node::create(array(
      'type' => 'simplenews_issue',
      'title' => $title,
      'uid' => '0',
      'status' => 1,
    ));
    $node->simplenews_issue->target_id = $this->getRandomNewsletter();
    $node->simplenews_issue->handler = 'simplenews_all';
    $node->save();

    // Send the node.
    \Drupal::service('simplenews.spool_storage')->addFromEntity($node);
    $node->save();

    // Send mails.
    \Drupal::service('simplenews.mailer')->sendSpool();
    \Drupal::service('simplenews.spool_storage')->clear();
    // Update sent status for newsletter admin panel.
    \Drupal::service('simplenews.mailer')->updateSendStatus();

    $mails = $this->drupalGetMails();

    // Check that the node title is displayed unaltered in the subject and
    // unaltered except being uppercased due to the HTML conversion in the body.
    $this->assertTrue(strpos($mails[0]['body'], strtoupper($title)) != FALSE);
    $this->assertTrue(strpos($mails[0]['subject'], $title) != FALSE);
  }
}
