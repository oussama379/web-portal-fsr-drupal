<?php

/**
 * @file
 * Contains \Drupal\field_collection\Plugin\Field\FieldFormatter\FieldCollectionLinksFormatter.
 */

namespace Drupal\field_collection\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Entity\ContentEntityInterface;

abstract class FieldCollectionLinksFormatter extends FormatterBase {

  /**
   * Helper function to get Edit and Delete links for an item.
   */
  protected function getEditLinks(FieldItemInterface $item) {
    $links = '';
    if ($item->getEntity()->access('update', \Drupal::currentUser())) {
      $links = '(' . \Drupal::l(t('Edit'), Url::FromRoute('entity.field_collection_item.edit_form', array('field_collection_item' => $item->value)));

      $links .= '|' . \Drupal::l(t('Delete'), Url::FromRoute('entity.field_collection_item.delete_form', array('field_collection_item' => $item->value)));

      $links .= ')';
    }

    return $links;
  }

  /***
   * Return a link to add a field collection item entity to this field.
   *
   * Returns a blank string if the field is at maximum capacity or the user
   * does not have access to edit it.
   */
  protected function getAddLink(ContentEntityInterface $host) {
    $link = '';

    if ($host->access('update', \Drupal::currentUser())) {
      $link = '<ul class="action-links action-links-field-collection-add"><li>';

      $link .= \Drupal::l(t('Add'), Url::FromRoute('field_collection_item.add_page', [
        'field_collection' => $this->fieldDefinition->getName(),
        'host_type' => $host->getEntityTypeId(),
        'host_id' => $host->id(),
      ]));

      $link .= '</li></ul>';
    }

    return($link);
  }

}
