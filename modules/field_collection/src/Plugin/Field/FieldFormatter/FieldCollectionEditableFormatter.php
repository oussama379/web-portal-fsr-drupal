<?php

/**
 * @file
 * Contains \Drupal\field_collection\Plugin\Field\FieldFormatter\FieldCollectionEditableFormatter
 */

namespace Drupal\field_collection\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'field_collection_editable' formatter.
 *
 * @FieldFormatter(
 *   id = "field_collection_editable",
 *   label = @Translation("Editable Field Collection Items"),
 *   field_types = {
 *     "field_collection"
 *   },
 * )
 */
class FieldCollectionEditableFormatter extends FieldCollectionLinksFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $count = 0; // TODO: Is there a better way to get an accurate count of the
                // items from the FileItemList that doesn't count blank items?
    $render_items = array();
    foreach ($items as $delta => $item) {
      if ($item->value !== NULL) {
        $count++;
        $to_render = \Drupal::entityTypeManager()->getViewBuilder('field_collection_item')->view($item->getFieldCollectionItem());

        $to_render['#suffix'] = $this->getEditLinks($item);
        $render_items[] = $to_render;
      }
    }

    $cardinality = $this->fieldDefinition
      ->getFieldStorageDefinition()
      ->getCardinality();

    if ($cardinality == -1 || $count < $cardinality) {
      $render_items['#suffix'] = '<ul class="action-links action-links-field-collection-add"><li>';
      $render_items['#suffix'] .= $this->getAddLink($items->getEntity());
      $render_items['#suffix'] .= '</li></ul>';
    }

    return $render_items;
  }
}
