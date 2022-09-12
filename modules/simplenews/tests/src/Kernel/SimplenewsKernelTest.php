<?php

namespace Drupal\Tests\simplenews\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Unit tests for certain functions.
 *
 * @group simplenews
 */
class SimplenewsKernelTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('simplenews');

  public function testMasking() {
    $this->assertEqual('t*****@e*****.org', simplenews_mask_mail('test@example.org'));
    $this->assertEqual('t*****@e*****.org', simplenews_mask_mail('t@example.org'));
    $this->assertEqual('t*****@t*****.org', simplenews_mask_mail('t@test.example.org'));
    $this->assertEqual('t*****@e*****', simplenews_mask_mail('t@example'));

  }
}
