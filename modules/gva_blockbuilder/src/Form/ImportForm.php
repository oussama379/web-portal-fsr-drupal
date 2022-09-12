<?php
namespace Drupal\gavias_blockbuilder\Form;
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
      $bid = 0;
      if(\Drupal::request()->attributes->get('bid')) $bid = \Drupal::request()->attributes->get('bid');

      if (is_numeric($bid)) {
        $bblock = db_select('{gavias_blockbuilder}', 'd')
           ->fields('d')
           ->condition('id', $bid, '=')
           ->execute()
           ->fetchAssoc();
      } else {
        $bblock = array('id' => 0, 'title' => '');
      }
      if($bblock['id']==0){
        drupal_set_message('Not found gavias block builder !');
        return false;
      }
      $form = array();
      $form['id'] = array(
          '#type' => 'hidden',
          '#default_value' => $bblock['id']
      );
      $form['title'] = array(
          '#type' => 'hidden',
          '#default_value' => $bblock['title']
      );
      $form['file'] = array(
        '#type' => 'managed_file',
        '#title' => t('Upload File Content'),
        '#description' => t('Upload your builder that exported before. Allowed extensions: .txt'),
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
      $this->setFormError('title', $form_state, $this->t('Please enter title for buider block.'));
    } 
   }

   /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if ($form['id']['#value']) {
      $params = '';

      if (!empty($values['file'][0])) {
        $fid = $values['file'][0];
        $file = File::load($fid);
        $read_file = \Drupal::service('file_system')->realpath($file->getFileUri());
        $params = file_get_contents($read_file);
      }

      $id = $form['id']['#value'];
      db_update("gavias_blockbuilder")
      ->fields(array(
        'params' => $params,
      ))
      ->condition('id', $id)
      ->execute();
      \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
      drupal_set_message("Block Builder '{$form['title']['#value']}' has been updated");
      $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_blockbuilder.admin.edit', array('bid'=>$id)));
      $response->send();
    }  
  }
}