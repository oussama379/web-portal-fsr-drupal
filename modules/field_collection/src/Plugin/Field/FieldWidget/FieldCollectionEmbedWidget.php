<?php

/**
 * @file
 * Contains \Drupal\field_collection\Plugin\Field\FieldWidget\FieldCollectionEmbedWidget.
 */

namespace Drupal\field_collection\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Render\Element;
use Drupal\field_collection\Entity\FieldCollectionItem;

/**
 * Plugin implementation of the 'field_collection_embed' widget.
 *
 * @FieldWidget(
 *   id = "field_collection_embed",
 *   label = @Translation("Embedded"),
 *   field_types = {
 *     "field_collection"
 *   },
 * )
 */
class FieldCollectionEmbedWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // TODO: Detect recursion
    $field_name = $this->fieldDefinition->getName();

    // Nest the field collection item entity form in a dedicated parent space,
    // by appending [field_name, delta] to the current parent space.
    // That way the form values of the field collection item are separated.
    $parents = array_merge($element['#field_parents'], array($field_name, $delta));

    $element += [
      '#element_validate' => [[static::class, 'validate']],
      '#parents' => $parents,
      '#field_name' => $field_name,
    ];

    if ($this->fieldDefinition->getFieldStorageDefinition()->getCardinality() == 1) {
      $element['#type'] = 'fieldset';
    }

    $field_state = static::getWidgetState($element['#field_parents'], $field_name, $form_state);

    if (isset($field_state['field_collection_item'][$delta])) {
      $field_collection_item = $field_state['field_collection_item'][$delta];
    }
    else {
      $field_collection_item = $items[$delta]->getFieldCollectionItem(TRUE);
      // Put our entity in the form state, so FAPI callbacks can access it.
      $field_state['field_collection_item'][$delta] = $field_collection_item;
    }

    static::setWidgetState($element['#field_parents'], $field_name, $form_state, $field_state);

    $display = entity_get_form_display('field_collection_item', $field_name, 'default');
    $display->buildForm($field_collection_item, $element, $form_state);

    if (empty($element['#required'])) {
      $element['#after_build'][] = [static::class, 'delayRequiredValidation'];

      // Stop HTML5 form validation so our validation code can run instead.
      $form['#attributes']['novalidate'] = 'novalidate';
    }

    // Put the remove button on unlimited cardinality field collection fields.
    if ($this->fieldDefinition->getFieldStorageDefinition()->getCardinality() == FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED) {
      $options = ['query' => ['element_parents' => implode('/', $element['#parents'])]];

      $element['actions'] = [
        '#type' => 'actions',
        'remove_button' => [
          '#delta' => $delta,
          '#name' => implode('_', $parents) . '_remove_button',
          '#type' => 'submit',
          '#value' => t('Remove'),
          '#validate' => [],
          '#submit' => [[static::class, 'removeSubmit']],
          '#limit_validation_errors' => [],
          '#ajax' => [
            'callback' => [$this, 'ajaxRemove'],
            'options' => $options,
            'effect' => 'fade',
            'wrapper' => $form['#wrapper_id'],
          ],
          '#weight' => 1000,
        ],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    // We don't want to render empty items on field collection fields
    // unless a) the field collection is empty ; b) the form is rebuilding,
    // which means that the user clicked on "Add another item"; or
    // c) we are creating a new entity.
    if ((count($items) > 0) && !$form_state->isRebuilding() && !$items->getEntity()->isNew()) {
      $field_name = $this->fieldDefinition->getName();
      $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
      $parents = $form['#parents'];
      if ($cardinality == FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED) {
        $field_state = static::getWidgetState($parents, $field_name, $form_state);
        $field_state['items_count']--;
        static::setWidgetState($parents, $field_name, $form_state, $field_state);
      }
    }

    // Adjust wrapper identifiers as they are shared between parents and
    // children in nested field collections.
    $form['#wrapper_id'] = Html::getUniqueID($items->getName());
    $elements = parent::formMultipleElements($items, $form, $form_state);
    $elements['#prefix'] = '<div id="' . $form['#wrapper_id'] . '">';
    $elements['#suffix'] = '</div>';
    $elements['add_more']['#ajax']['wrapper'] = $form['#wrapper_id'];
    return $elements;
  }

  /**
   * #after_build of a field collection element.
   *
   * Delays the validation of #required.
   */
  public static function delayRequiredValidation($element, FormStateInterface $form_state) {
    // If the process_input flag is set, the form and its input is going to be
    // validated. Prevent #required (sub)fields from throwing errors while
    // their non-#required field collection item is empty.
    if ($form_state->isProcessingInput()) {
      static::collectRequiredElements($element, $element['#field_collection_required_elements']);
    }
    return $element;
  }

  /**
   * Prevent the default 'required' validation from running on subfields.
   */
  private static function collectRequiredElements(&$element, &$required_elements) {
    // Recurse through all children.
    foreach (Element::children($element) as $key) {
      if (isset($element[$key]) && $element[$key]) {
        static::collectRequiredElements($element[$key], $required_elements);
      }
    }

    if (!empty($element['#required'])) {
      $element['#required'] = FALSE;
      $required_elements[] = &$element;
      $element += array('#pre_render' => array());
      array_unshift($element['#pre_render'], [static::class, 'renderRequired']);
    }
  }

  /**
   * #pre_render callback ensures the element is rendered as being required.
   */
  public static function renderRequired($element) {
    $element['#required'] = TRUE;
    return $element;
  }

  /**
   * FAPI validation of an individual field collection element.
   */
  public static function validate($element, FormStateInterface $form_state, $form) {
    $field_parents = $element['#field_parents'];
    $field_name = $element['#field_name'];

    $field_state = static::getWidgetState($field_parents, $field_name, $form_state);

    $field_collection_item = $field_state['field_collection_item'][$element['#delta']];

    $display = entity_get_form_display('field_collection_item', $field_name, 'default');
    $display->extractFormValues($field_collection_item, $element, $form_state);

    // Now validate required elements if the entity is not empty.
    if (!$field_collection_item->isEmpty() && !empty($element['#field_collection_required_elements'])) {
      foreach ($element['#field_collection_required_elements'] as &$elements) {
        // Copied from \Drupal\Core\Form\FormValidator::doValidateForm().
        // #1676206: Modified to support options widget.
        if (isset($elements['#needs_validation'])) {
          $is_empty_multiple = (!count($elements['#value']));
          $is_empty_string = (is_string($elements['#value']) && Unicode::strlen(trim($elements['#value'])) == 0);
          $is_empty_value = ($elements['#value'] === 0);
          $is_empty_option = (isset($elements['#options']['_none']) && $elements['#value'] == '_none');

          if ($is_empty_multiple || $is_empty_string || $is_empty_value || $is_empty_option) {
            if (isset($elements['#required_error'])) {
              $form_state->setError($elements, $elements['#required_error']);
            }
            else if (isset($elements['#title'])) {
              $form_state->setError($elements, t('@name field is required.', array('@name' => $elements['#title'])));
            }
            else {
              $form_state->setError($elements);
            }
          }
        }
      }
    }

    // Only if the form is being submitted, finish the collection entity and
    // prepare it for saving.
    if ($form_state->isSubmitted() && !$form_state->hasAnyErrors()) {
      // Load initial form values into $item, so any other form values below the
      // same parents are kept.
      $field = NestedArray::getValue($form_state->getValues(), $element['#parents']);

      // Set the _weight if it is a multiple field.
      $element_widget = NestedArray::getValue($form, array_slice($element['#array_parents'], 0, -1));
      if (isset($element['_weight']) && $element_widget['#cardinality_multiple']) {
        $field['_weight'] = $element['_weight']['#value'];
      }

      // Put the field collection field in $field['field_collection_item'], so
      // it is saved with the host entity via FieldCollection->preSave() / field
      // API if it is not empty.
      $field['field_collection_item'] = $field_collection_item;
      $form_state->setValue($element['#parents'], $field);
    }
  }

  /**
   * Submit callback to remove an item from the field UI multiple wrapper.
   *
   * When a remove button is submitted, we need to find the item that it
   * referenced and delete it. Since field UI has the deltas as a straight
   * unbroken array key, we have to renumber everything down. Since we do this
   * we *also* need to move all the deltas around in the $form_state->values
   * and $form_state input so that user changed values follow. This is a bit
   * of a complicated process.
   */
  public static function removeSubmit($form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();
    $delta = $button['#delta'];

    // Where in the form we'll find the parent element.
    $address = array_slice($button['#array_parents'], 0, -4);
    $address_state = array_slice($button['#parents'], 0, -3);

    // Go one level up in the form, to the widgets container.
    $parent_element = NestedArray::getValue($form, array_merge($address, array('widget')));

    $field_name = $parent_element['#field_name'];
    $parents = $parent_element['#field_parents'];

    $field_state = static::getWidgetState($parents, $field_name, $form_state);

    // Go ahead and renumber everything from our delta to the last
    // item down one. This will overwrite the item being removed.
    for ($i = $delta; $i <= $field_state['items_count']; $i++) {
      $old_element_address = array_merge($address, array('widget', $i + 1));
      $old_element_state_address = array_merge($address_state, array($i + 1));
      $new_element_state_address = array_merge($address_state, array($i));

      $moving_element = NestedArray::getValue($form, $old_element_address);

      $moving_element_value = NestedArray::getValue($form_state->getValues(), $old_element_state_address);

      $moving_element_input = NestedArray::getValue($form_state->getUserInput(), $old_element_state_address);

      // Tell the element where it's being moved to.
      $moving_element['#parents'] = $new_element_state_address;

      // Move the element around.
      $form_state->setValueForElement($moving_element, $moving_element_value);
      $user_input = $form_state->getUserInput();
      NestedArray::setValue($user_input, $moving_element['#parents'], $moving_element_input);
      $form_state->setUserInput($user_input);

      // Move the entity in our saved state.
      if (isset($field_state['field_collection_item'][$i + 1])) {
        $field_state['field_collection_item'][$i] = $field_state['field_collection_item'][$i + 1];
      }
      else {
        unset($field_state['field_collection_item'][$i]);
      }
    }

    // Replace the deleted entity with an empty one. This helps to ensure that
    // trying to add a new entity won't ressurect a deleted entity from the
    // trash bin.
    $count = count($field_state['field_collection_item']);

    $field_state['field_collection_item'][$count] = FieldCollectionItem::create(['field_name' => $field_name]);

    // Then remove the last item. But we must not go negative.
    if ($field_state['items_count'] > 0) {
      $field_state['items_count']--;
    }

    // Fix the weights. Field UI lets the weights be in a range of
    // (-1 * item_count) to (item_count). This means that when we remove one,
    // the range shrinks; weights outside of that range then get set to
    // the first item in the select by the browser, floating them to the top.
    // We use a brute force method because we lost weights on both ends
    // and if the user has moved things around, we have to cascade because
    // if I have items weight weights 3 and 4, and I change 4 to 3 but leave
    // the 3, the order of the two 3s now is undefined and may not match what
    // the user had selected.
    $input = NestedArray::getValue($form_state->getUserInput(), $address);
    // Sort by weight.
    uasort($input, '_field_collection_sort_items_helper');

    // Reweight everything in the correct order.
    $weight = -1 * $field_state['items_count'];
    foreach ($input as $key => $item) {
      if ($item) {
        $input[$key]['_weight'] = $weight++;
      }
    }

    $user_input = $form_state->getUserInput();
    NestedArray::setValue($user_input, $address, $input);
    $form_state->setUserInput($user_input);

    static::setWidgetState($parents, $field_name, $form_state, $field_state);

    $form_state->setRebuild();
  }

  /**
   * Ajax callback to remove a field collection from a multi-valued field.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An AjaxResponse object.
   *
   * @see self::removeSubmit()
   */
  function ajaxRemove(array $form, FormStateInterface &$form_state) {
    // At this point, $this->removeSubmit() removed the element so we just need
    // to return the parent element.
    $button = $form_state->getTriggeringElement();
    return NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -3));
  }

}
