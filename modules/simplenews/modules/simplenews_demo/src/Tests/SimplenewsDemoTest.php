<?php
/**
 * @file
 * Contains \Drupal\simplenews_demo\Tests\SimplenewsDemoTest.
 */

namespace Drupal\simplenews_demo\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the demo module for Simplenews.
 *
 * @group simplenews
 */
class SimplenewsDemoTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  public static $modules = [];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    // Install bartik theme.
    \Drupal::service('theme_handler')->install(array('bartik'));
    $theme_settings = $this->config('system.theme');
    $theme_settings->set('default', 'bartik')->save();
    // Install simplenews_demo module.
    \Drupal::service('module_installer')->install(['simplenews_demo']);
    // Log in with all relevant permissions.
    $this->drupalLogin($this->drupalCreateUser(['administer simplenews subscriptions', 'send newsletter', 'administer newsletters', 'administer simplenews settings']));
  }

  /**
   * Asserts the demo module has been installed successfully.
   */
  protected function testInstalled() {
    // Check for the two subscription blocks.
    $this->assertText('Simplenews multiple subscriptions');
    $this->assertText('Stay informed - subscribe to our newsletters.');
    $this->assertText('Simplenews subscription');
    $this->assertText('Stay informed - subscribe to our newsletter.');

    $this->drupalGet('admin/config/services/simplenews');
    $this->clickLink(t('Edit'));
    // Assert default description is present.
    $this->assertEqual('This is an example newsletter. Change it.', (string) $this->xpath('//textarea[@id="edit-description"]')[0]);
    $from_name = $this->xpath('//input[@id="edit-from-name"]')[0];
    $from_address = $this->xpath('//input[@id="edit-from-address"]')[0];
    $this->assertEqual('Drupal', (string) $from_name['value']);
    $this->assertEqual('simpletest@example.com', (string) $from_address['value']);
    // Assert demo newsletters.
    $this->drupalGet('admin/config/services/simplenews');
    $this->assertText(t('Press releases'));
    $this->assertText(t('Special offers'));
    $this->assertText(t('Weekly content update'));
    // Assert demo newsletters sent.
    $this->drupalGet('admin/content/simplenews');
    $this->assertText('Scheduled weekly content newsletter issue');
    $this->assertText('Sent press releases');
    $this->assertText('Unpublished press releases');
    $this->assertText('Pending special offers');
    $this->assertText('Stopped special offers');
    $this->assertText('Scheduled weekly content newsletter issue - Week ');
    $this->assertRaw(t('Newsletter issue sent to 2 subscribers.'));
    $this->assertRaw(t('Newsletter issue is pending, 0 mails sent out of 3.'));
    $this->assertRaw(t('Newsletter issue is pending, 0 mails sent out of 1.'));
    // Assert demo subscribers.
    $this->drupalGet('admin/people/simplenews');
    $this->assertText('a@example.com');
    $this->assertText('b@example.com');
    $this->assertText('demouser1@example.com');
  }

}
