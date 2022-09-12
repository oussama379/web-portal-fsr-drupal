<?php

/**
 * @file
 * Contains \Drupal\field_collection\FieldCollectionForm.
 */

namespace Drupal\field_collection;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form controller for field collection forms.
 */
class FieldCollectionForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $field_collection = $this->entity;

    if ($this->operation == 'add') {
      // There should be no way to attempt to add a field collection through
      // this form but set up a message for it just in case.
      $form['#title'] = $this->t('Add field collection');
      drupal_set_message(t('To add a field collection create a field of type field collection on the host entity type.'));
    }
    else {
      $form['#title'] = $this->t('Edit %label field collection', array('%label' => $field_collection->label()));

      // TODO: Add links to edit the field for this collection in each of its
      // host bundles.
      $form['help'] = array(
        '#type' => 'markup',
        '#markup' => t('<p>There are no options to edit for field collection bundles.</p><p><a href="@url">Manage fields inside this collection.</a></p>', array(
          '@url' => Url::fromRoute('entity.field_collection_item.field_ui_fields', [
            $field_collection->getEntityTypeId() => $field_collection->id()
          ]),
        )));
    }

    return $form;
  }

}
