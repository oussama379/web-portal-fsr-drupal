<?php

/**
 * @file
 * Contains \Drupal\field_collection\Plugin\Field\FieldFormatter\FieldCollectionListFormatter.
 */

namespace Drupal\field_collection\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'field_collection_list' formatter.
 *
 * @FieldFormatter(
 *   id = "field_collection_list",
 *   label = @Translation("List"),
 *   field_types = {
 *     "field_collection"
 *   },
 * )
 */
class FieldCollectionListFormatter extends FieldCollectionLinksFormatter {

  /**
   * {@inheritdoc}
   *
   * TODO: Use $langcode.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();
    $settings = $this->getFieldSettings();
    $count = 0; // TODO: Is there a better way to get an accurate count of the
                // items from the FieldItemList that doesn't count blank items?
                // Possibly \Countable->count()?

    $storage = \Drupal::entityTypeManager()->getStorage('field_collection_item');
    foreach ($items as $delta => $item) {
      if ($item->value !== NULL) {
        $count++;

        $field_collection_item = $storage->loadRevision($item->revision_id);

        if ($field_collection_item->isDefaultRevision()) {
          $links = \Drupal::l($this->fieldDefinition->getName() . ' ' . $delta, Url::FromRoute('entity.field_collection_item.canonical', array('field_collection_item' => $item->value)));

          $links .= ' ' . $this->getEditLinks($item);
        }
        else {
          $links = \Drupal::l($this->fieldDefinition->getName() . ' ' . $delta, Url::FromRoute('field_collection_item.revision_show', [
            'field_collection_item' => $item->value,
            'field_collection_item_revision' => $item->revision_id,
          ]));
        }

        $element[$delta] = array('#markup' => $links);
      }
    }

    $cardinality = $this->fieldDefinition
      ->getFieldStorageDefinition()
      ->getCardinality();

    if ($cardinality == -1 || $count < $cardinality) {
      $element['#suffix'] = '<ul class="action-links action-links-field-collection-add"><li>';
      $element['#suffix'] .= $this->getAddLink($items->getEntity());
      $element['#suffix'] .= '</li></ul>';
    }

    return $element;
  }

}
