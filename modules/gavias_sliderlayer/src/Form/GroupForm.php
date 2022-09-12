<?php
namespace Drupal\gavias_sliderlayer\Form;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation;
class GroupForm implements FormInterface {
   /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
   public function getFormID() {
      return 'add_form';
   }

   /**
    * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
   public function buildForm(array $form, FormStateInterface $form_state) {
      $sid = 0;
      if(\Drupal::request()->attributes->get('sid')) $sid = \Drupal::request()->attributes->get('sid');
      
      if (is_numeric($sid)) {
        $slide = db_select('{gavias_sliderlayergroups}', 'd')->fields('d')->condition('id', $sid, '=')->execute()->fetchAssoc();
        } else {
            $slide = array('id' => 0, 'title' => '');
        }
        $form = array();
        $form['id'] = array(
            '#type' => 'hidden',
            '#default_value' => $slide['id']
        );
        $form['title'] = array(
            '#type' => 'textfield',
            '#title' => 'Title',
            '#default_value' => $slide['title']
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => 'Save'
        );

      $form['actions'] = array('#type' => 'actions');
      $form['submit'] = array(
        '#type' => 'submit',
        '#value' => 'Save'
      );
    return $form;
   }

   /**
   * Implements \Drupal\Core\Form\FormInterface::validateForm().
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
      if (isset($form['values']['title']) && $form['values']['title'] === '' ) {
         $this->setFormError('title', $form_state, $this->t('Please enter title for slider.'));
       } 
   }

   /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if (is_numeric($form['id']['#value']) && $form['id']['#value'] > 0) {
      $sid = db_update("gavias_sliderlayergroups")
        ->fields(array(
            'title' => $form['title']['#value'],
        ))
        ->condition('id', $form['id']['#value'])
        ->execute();
        \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
      drupal_set_message("Slide '{$form['title']['#value']}' has been updated");
    } else {
        $sid = db_insert("gavias_sliderlayergroups")
          ->fields(array(
              'title' => $form['title']['#value'],
              'params' => ''
          ))
          ->execute();
        drupal_set_message("Slide '{$form['title']['#value']}' has been created");
        \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    }
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_sl_group.admin'));
    $response->send();
   }
}