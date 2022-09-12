<?php

namespace Drupal\simplenews\Tests;

/**
 * Test subscription output on user profile page.
 *
 * @group simplenews
 */
class SimplenewsTestSubscriptionOutput extends SimplenewsTestBase {

  /**
   * Test subscription output visibility for different users.
   */
  public function testSubscriptionVisiblity() {

    // Enable the extra field.
    entity_get_display('user', 'user', 'default')
      ->setComponent('simplenews', array(
          'label' => 'hidden',
          'type' => 'simplenews',
        ))
      ->save();

    // Create admin user.
    $admin_user = $this->drupalCreateUser(array(
      'administer users',
    ));
    // Create user that can view user profiles.
    $user = $this->drupalCreateUser(array(
      'access user profiles',
      'subscribe to newsletters',
      'access content',
    ));
    $this->drupalLogin($admin_user);
    // Tests extra fields for admin user.
    $this->drupalGet('user/' . $admin_user->id());
    $this->assertLink('Manage subscriptions');
    $this->drupalLogout();
    // Tests extra fields for user.
    $this->drupalLogin($user);
    $this->drupalGet('user/' . $admin_user->id());
    $this->assertNoLink('Manage subscriptions');
    $this->drupalGet('user/' . $user->id());
    $this->assertLink('Manage subscriptions');
    $this->drupalLogout();
    // Tests extra fields for anonymous users.
    $this->drupalGet('user/' . $admin_user->id());
    $this->assertNoLink('Manage subscriptions');
    $this->drupalGet('user/' . $user->id());
    $this->assertNoLink('Manage subscriptions');
  }
}
