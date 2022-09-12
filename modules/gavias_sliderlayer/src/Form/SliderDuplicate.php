<?php
namespace Drupal\gavias_sliderlayer\Form;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation;
class SliderDuplicate implements FormInterface {
   /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
   public function getFormID() {
      return 'slider_duplicate';
   }

   /**
    * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
   public function buildForm(array $form, FormStateInterface $form_state) {
      $id = 0;
      if(\Drupal::request()->attributes->get('id')) $id = \Drupal::request()->attributes->get('id');
      
      if (is_numeric($id)) {
        $slide = db_select('{gavias_sliderlayers}', 'd')
                 ->fields('d')
                 ->condition('id', $id, '=')
                 ->execute()->fetchAssoc();
        } else {
            $slide = array('id' => 0, 'title' => '', 'sort_index' => 1, 'group_id' => 0, 'params' => '', 'layersparams' => '', 'status' => 0, 'background_image_uri' => 0);
        }

        $form = array();
        $form['id'] = array(
            '#type' => 'hidden',
            '#default_value' => $slide['id']
        );
        $form['title'] = array(
            '#type' => 'textfield',
            '#title' => t('Title'),
            '#default_value' => t('Duplicate ') . $slide['title']
        );
        $form['sort_index'] = array(
          '#type' => 'hidden',
          '#default_value' => $slide['sort_index']
        );
        $form['group_id'] = array(
          '#type' => 'hidden',
          '#default_value' => $slide['group_id']
        );
        $form['params'] = array(
          '#type' => 'hidden',
          '#default_value' => $slide['params']
        );
        $form['layersparams'] = array(
          '#type' => 'hidden',
          '#default_value' => $slide['layersparams']
        );
        $form['status'] = array(
          '#type' => 'hidden',
          '#default_value' => $slide['status']
        );
        $form['background_image_uri'] = array(
          '#type' => 'hidden',
          '#default_value' => $slide['background_image_uri']
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Save')
        );

      $form['actions'] = array('#type' => 'actions');
      $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save')
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
      db_insert("gavias_sliderlayers")
      ->fields(array(
        'title'         => $form['title']['#value'],
        'group_id'      => $form['group_id']['#value'],
        'sort_index'    => $form['sort_index']['#value'],
        'params'        => $form['params']['#value'],
        'layersparams'  => $form['layersparams']['#value'],
        'status'        => $form['status']['#value'],
        'background_image_uri' => $form['background_image_uri']['#value']
      ))
      ->execute();
      drupal_set_message("Slide '{$form['title']['#value']}' has been duplicate");
      \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    }
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_sl_sliders.admin.list', array('gid' => $form['group_id']['#value'])));
    $response->send();
   }
}