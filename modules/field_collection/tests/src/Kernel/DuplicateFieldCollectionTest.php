<?php

/**
 * @file
 * Contains \Drupal\Tests\field_collection\Kernel\DuplicateFieldCollectionTest.
 */

namespace Drupal\Tests\field_collection\Kernel;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field_collection\Entity\FieldCollection;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests importing field_collection from config.
 *
 * @group field_collection
 */
class DuplicateFieldCollectionTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['field', 'field_collection', 'node', 'user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['field_collection']);
  }

  /**
   * Ensures field_collection_field_storage_config_insert() works correctly.
   */
  public function testDuplicateFieldCollection() {
    $values = [
      'type' => 'field_collection',
      'field_name' => 'my_collection',
      'label' => 'My Collection',
    ];
    FieldStorageConfig::create($values + ['entity_type' => 'user'])->save();
    // Change the label of the field collection.
    $field_collection_first = FieldCollection::load('my_collection');
    $field_collection_first->set('label', 'A new label');
    $field_collection_first->save();

    FieldStorageConfig::create($values + ['entity_type' => 'node'])->save();

    // If field_collection_field_storage_config_insert() had created a new field
    // collection, it would not have the custom name.
    $field_collection_second = FieldCollection::load('my_collection');
    $this->assertSame('A new label', $field_collection_second->label());
  }

}
