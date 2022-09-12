<?php

/**
 * Plugin implementation of the 'blockbuilder_builder' widget.
 *
 * @FieldWidget(
 *   id = "blockbuilder_widget",
 *   label = @Translation("Block Builder"),
 *   field_types = {
 *     "blockbuilder"
 *   }
 * )
 */
namespace Drupal\gavias_blockbuilder\Plugin\Field\FieldWidget;

use Drupal\gavias_blockbuilder\BuilderBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

class BlockBuilderWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    if (isset($form['#parents'][0]) && $form['#parents'][0] == 'default_value_input' && !empty($element['#field_parents'][0] && $element['#field_parents'][0] == 'default_value_input')) {
      $element['bid'] = array(
        '#type' => 'hidden',
        '#default_value' => 0,
      );

      $element['markup'] = array(
        '#type' => 'markup',
        '#markup' => t('<em>Default value settings for Block Builder field is not available.</em>')
      );
      return $element;

    }
    $parent_entity = $items->getEntity();
    $entity = $parent_entity;

    $user = \Drupal::currentUser();

    if (!$user->hasPermission('administer gaviasblockbuilder')) {
      return;
    }
    
    $langcode = $items->getLangcode();

    $field_name = $items->getName();
    $input = $form_state->getUserInput();

    $results = db_select('{gavias_blockbuilder}', 'd')
      ->fields('d', array('id', 'title', 'body_class'))
      ->orderBy('title', 'ASC')
      ->execute();

    $list_gbb = array( ''   => 'Disable');
    foreach ($results as $key => $result) {
      $list_gbb[$result->id] = $result->title . ' (' . $result->body_class . ')';
    }

    $bid = !empty($items[$delta]->bid) ? $items[$delta]->bid : '';
    
    $random = gavias_blockbuilder_makeid(10);

    $links = array(
      '#type' => 'link',
      '#title' => t('<strong>Add new builder</strong>'),
      '#url' => Url::fromRoute('gavias_blockbuilder.admin.add_popup', array('random'=>$random)),
      '#attributes' => array(
        'class' => array('use-ajax'),
        'data-dialog-type' => 'modal',
        'data-dialog-options' =>  json_encode(array(
            'resizable' => TRUE,
            'width' => '80%',
            'height' => 'auto',
            'max-width' => '1100px',
            'modal' => TRUE,
          )),
        'title' => t('Add new builder'),
      ),
    );
    $element['addform'] = $links;
    
    $element['bid'] = array(
      '#title' => $items->getFieldDefinition()->getLabel() . (' <a class="gva-popup-iframe" href="'.\Drupal::url('gavias_blockbuilder.admin').'">Manage All Blockbuilders</a>'),
      '#type' => 'textfield',
      '#default_value' => $bid,
      '#attributes' => array('class' => array('field_block_builder', 'gva-id-' . $random), 'data-random' => $random, 'readonly'=>'readonly')
    );

    $element['choose_gbb'] = array(
      '#type' => 'markup',
      '#markup' => $this->_get_list_blockbuilder($random),
      '#allowed_tags' => array('a', 'div', 'span')
    );
    return $element;
  }

  function _get_list_blockbuilder($random){
     $results = db_select('{gavias_blockbuilder}', 'd')
      ->fields('d', array('id', 'title', 'body_class'))
      ->orderBy('title', 'ASC')
      ->execute();
      $html = '<div class="gva-choose-gbb gva-id-'.$random.'">';
      $html .= '<span class="gbb-item disable"><a class="select" data-id="" title="disable">Disable</a></span>';
      foreach ($results as $key => $result) {
        $html .= '<span class="gbb-item id-'.$result->id.'">';
        $html .= '<a class="select" data-id="'.$result->id.'" title="'. $result->body_class .'">' . $list_gbb[$result->id] = $result->title  . '</a>';
        $html .= ' <span class="action">( <a class="edit gva-popup-iframe" href="'.\Drupal::url('gavias_blockbuilder.admin.edit', array('bid'=>$result->id)).'" data-id="'.$result->id.'" title="'. $result->body_class .'">Edit</a>';
        $html .= ' | <a class="duplicate use-ajax" data-dialog-type="modal" data-dialog-options="{"resizable":true,"width":"80%","height":"auto","max-width":"1100px","modal":true}" href="'.\Drupal::url('gavias_blockbuilder.admin.duplicate_popup', array('bid'=>$result->id, 'random'=>$random)).'" data-id="'.$result->id.'" title="'. $result->body_class .'">Duplicate</a>';
        $html .= ' | <a class="import use-ajax" data-dialog-type="modal" data-dialog-options="{"resizable":true,"width":"80%","height":"auto","max-width":"1100px","modal":true}" href="'.\Drupal::url('gavias_blockbuilder.admin.import_popup', array('bid'=>$result->id)).'" data-id="'.$result->id.'" title="'. $result->body_class .'">Import</a> ';
        $html .= ' | <a class="export" href="'.\Drupal::url('gavias_blockbuilder.admin.export', array('bid'=>$result->id)).'" data-id="'.$result->id.'" title="'. $result->body_class .'">Export</a>';
        $html .= ' | <a class="delete use-ajax" data-dialog-type="modal" data-dialog-options="{"resizable":true,"width":"80%","height":"auto","max-width":"1100px","modal":true}" href="'.\Drupal::url('gavias_blockbuilder.admin.delete_popup', array('bid'=>$result->id, 'random'=>$random)).'" data-id="'.$result->id.'" title="'. $result->body_class .'">Delete</a> )</span>';
        $html .= '</span>';
      }
      $html .= '</div>';
      return $html;
  }
}