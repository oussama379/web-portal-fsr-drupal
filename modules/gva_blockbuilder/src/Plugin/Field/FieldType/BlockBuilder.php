<?php

/**
 * Plugin implementation of the 'blockbuilder' field type.
 *
 * @FieldType(
 *   id = "blockbuilder",
 *   label = @Translation("Block Builder"),
 *   module = "gavias_blockbuilder",
 *   description = @Translation("Blockbuilder field."),
 *   default_widget = "blockbuilder_widget",
 *   default_formatter = "blockbuilder_formatter"
 * )
 */

namespace Drupal\gavias_blockbuilder\Plugin\Field\FieldType;

use Drupal\gavias_blockbuilder\BuilderBase;
use Drupal\Core\Database;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;


class BlockBuilder extends FieldItemBase {

  /**
   * {Inheritdoc}
   */

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['bid'] = DataDefinition::create('integer')
      ->setLabel(t('Builder ID'))
      ->setDescription(t('A Builder ID referenced the BlockBuilder'));
    return $properties;
  }
  
  /**
   * {Inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $columns = array(
      'bid' => array(
        'description' => 'The Block buider ID being referenced in this field.',
        'type' => 'int',
        'unsigned' => TRUE,
      ),
    );

    $schema = array(
      'columns' => $columns,
      'indexes' => array(
        'bid' => array('bid'),
      ),
    );

    return $schema;
  }

  public function isEmpty() {
    $value = $this->get('bid')->getValue();
    return $value === NULL || $value === '';
  }
}