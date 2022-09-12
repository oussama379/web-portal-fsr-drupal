<?php
namespace Drupal\gavias_sliderlayer\Form;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation;
use Drupal\file\Entity\File;

class ImportForm implements FormInterface {
   /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
   public function getFormID() {
      return 'import_form';
   }

   /**
    * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
   public function buildForm(array $form, FormStateInterface $form_state) {
      $gid = 0;
      if(\Drupal::request()->attributes->get('gid')) $gid = \Drupal::request()->attributes->get('gid');

      if (is_numeric($gid)) {
        $group = db_select('{gavias_sliderlayergroups}', 'd')
           ->fields('d')
           ->condition('id', $gid, '=')
           ->execute()
           ->fetchAssoc();
      } else {
        $group = array('id' => 0, 'title' => '');
      }
      if($group['id']==0){
        drupal_set_message('Not found gavias slider layer !');
        return false;
      }

      $form = array();
      
      $form['gid'] = array(
        '#type' => 'hidden',
        '#default_value' => $group['id']
      );

      $form['title'] = array(
          '#type' => 'textfield',
          '#value' => $group['title'],
          '#attributes' => array('readonly' => 'readonly')
      );

      $form['file'] = array(
        '#type' => 'managed_file',
        '#title' => t('Upload File Content'),
        '#description' => t('Upload your sliderlayer that exported before. Allowed extensions: .txt'),
        '#upload_location' => 'public://',
        '#upload_validators' => array(
            'file_validate_extensions' => array('txt'),
            // Pass the maximum file size in bytes
            'file_validate_size' => array(1024 * 1280 * 800),
        ),
        '#required' => TRUE,
      );
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
      $this->setFormError('title', $form_state, $this->t('Please enter title for slider layer.'));
    } 
   }

   /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if ($form['gid']['#value']) {
      $data = '';

      if (!empty($values['file'][0])) {
        $fid = $values['file'][0];
        $file = File::load($fid);
        $read_file = \Drupal::service('file_system')->realpath($file->getFileUri());
        $data = file_get_contents($read_file);
      }

      $gid = $form['gid']['#value'];
      $json = base64_decode($data);
      $slideshow = json_decode($json);

      db_update("gavias_sliderlayergroups")
      ->fields(array(
        'params' => (isset($slideshow->group->params) && $slideshow->group->params) ? $slideshow->group->params : ''
      ))
      ->condition('id', $gid)
      ->execute();
    
      $i = 0; 
      if($slideshow->sliders){
        db_delete('gavias_sliderlayers')->condition('group_id', $gid)->execute();
        foreach ($slideshow->sliders as $key => $slider) {
          $i++;
          db_insert("gavias_sliderlayers")
            ->fields(array(
              'sort_index' => (isset($slider->sort_index) && $slider->sort_index) ? $slider->sort_index : $i,
              'status' => (isset($slider->status) && $slider->status) ? $slider->status : 1,
              'title' => (isset($slider->title) && $slider->title) ? $slider->title : 'Title',
              'group_id' => $gid,
              'params'  => (isset($slider->params) && $slider->params) ? $slider->params : '', 
              'layersparams' => $slider->layersparams,
              'background_image_uri' => (isset($slider->background_image_uri) && $slider->background_image_uri) ? $slider->background_image_uri : '',
          ))
          ->execute();
        }
      }  

      \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
      drupal_set_message("Slider Layer '{$form['title']['#value']}' has been import");
      $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_sl_group.admin', array('gid'=>$gid)));
      $response->send();
    }  
  }
}