<?php

/**
 * @file
 * Contains \Drupal\field_collection\Plugin\Field\FieldFormatter\FieldCollectionItemsFormatter
 */

namespace Drupal\field_collection\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'field_collection_items' formatter.
 *
 * @FieldFormatter(
 *   id = "field_collection_items",
 *   label = @Translation("Field Collection Items"),
 *   field_types = {
 *     "field_collection"
 *   },
 * )
 */
class FieldCollectionItemsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $render_items = array();
    foreach ($items as $delta => $item) {
      if ($item->value !== NULL) {
        $render_items[] = \Drupal::entityTypeManager()->getViewBuilder('field_collection_item')->view($item->getFieldCollectionItem());
      }
    }
    return $render_items;
  }
}
