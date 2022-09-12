<?php


/**
 * @file
 * Provide relationship handler for field collection fields.
 *
 * Definition of Drupal\field_collection\Plugin\views\relationship\FieldCollectionHandlerRelationship.
 */

namespace Drupal\field_collection\Plugin\views\relationship;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\views\Views;
use Drupal\views\Plugin\views\relationship\RelationshipPluginBase;

/**
 * Relationship handler to return the taxonomy terms of nodes.
 *
 * @ingroup views_relationship_handlers
 *
 * @ViewsRelationship("field_collection_handler_relationship")
 */
class FieldCollectionHandlerRelationship extends RelationshipPluginBase  {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['delta'] = array('default' => -1);

    return $options;
  }

  /**
   * Add a delta selector for multiple fields.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $field = FieldStorageConfig::loadByName($this->definition['target entity type'], $this->definition['field name']);
    $cardinality = $field->getCardinality();

    // Only add the delta selector if the field is multiple.
    if ($field->isMultiple()) {
      $max_delta = ($cardinality == FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED) ? 10 : $cardinality;

      $options = array('-1' => t('All'));
      for ($i = 0; $i < $max_delta; $i++) {
        $options[$i] = $i + 1;
      }
      $form['delta'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => $this->options['delta'],
        '#title' => t('Delta'),
        '#description' => t('The delta allows you to select which item in a multiple value field to key the relationship off of. Select "1" to use the first item, "2" for the second item, and so on. If you select "All", each item in the field will create a new row, which may appear to cause duplicates.'),
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function ensureMyTable() {

    $field = FieldStorageConfig::loadByName($this->definition['target entity type'], $this->definition['field name']);
    $cardinality = $field->getCardinality();

    if (!isset($this->tableAlias)) {
      $join = $this->getJoin();
      if ($this->options['delta'] != -1 && $cardinality) {
        $join->extra[] = array(
          'field' => 'delta',
          'value' => $this->options['delta'],
          'numeric' => TRUE,
        );
      }
      $this->tableAlias = $this->query->ensureTable($this->table, $this->relationship, $join);
      return $this->tableAlias;
    }
  }
}
