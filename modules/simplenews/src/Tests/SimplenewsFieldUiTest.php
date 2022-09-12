<?php

namespace Drupal\simplenews\Tests;

/**
 * Tests integration with field_ui.
 *
 * @group simplenews
 */
class SimplenewsFieldUiTest extends SimplenewsTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('field_ui', 'help');

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->drupalPlaceBlock('help_block');
  }

  /**
   * Test that a new content type has a simplenews_issue field when is used as a simplenews newsletter.
   */
  function testContentTypeCreation() {
    $admin_user = $this->drupalCreateUser(array(
      'administer blocks',
      'administer content types',
      'administer nodes',
      'administer node fields',
      'administer node display',
      'access administration pages',
      'administer permissions',
      'administer newsletters',
      'administer simplenews subscriptions',
      'administer simplenews settings',
      'bypass node access',
      'send newsletter',
    ));
    $this->drupalLogin($admin_user);

    $this->drupalGet('admin/structure/types');
    $this->clickLink(t('Add content type'));
    $edit = array(
      'name' => $name = 'simplenews_issue',
      'type' => $type = strtolower($name),
      'simplenews_content_type' => TRUE,
    );
    $this->drupalPostForm(NULL, $edit, t('Save and manage fields'));
    $this->drupalGet('admin/structure/types/manage/' . $type . '/fields');
    $this->assertText('simplenews_issue');
    // Check if the help text is displayed.
    $this->drupalGet('admin/structure/types/manage/' . $type . '/display');
    $this->assertText("'Plain' display settings apply to the content of emails");
    $this->drupalGet('admin/config/services/simplenews/settings/newsletter');
    $this->assertText('These settings are default to all newsletters. Newsletter specific settings');
  }
}
