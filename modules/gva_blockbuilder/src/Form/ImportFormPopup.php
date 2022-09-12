<?php
namespace Drupal\gavias_blockbuilder\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
class ImportFormPopup extends FormBase{

  /**
   * {@inheritdoc}.
   */
  public function getFormId(){
    return 'duplicate_form_import';
  }

  /**
   * {@inheritdoc}.
  */
  public function buildForm(array $form, FormStateInterface $form_state){
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

    $form['builder-dialog-messages'] = array(
      '#markup' => '<div id="builder-dialog-messages"></div>',
    );
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
    $form['actions']['submit'] = array(
      '#value' => t('Submit'),
      '#type' => 'submit',
      '#ajax' => array(
        'callback' => '::modal',
      ),
    );
  return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   * Submit handle for adding Element
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
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
    }  
  }

  public function getFormArgs($form_state){
    $args = array();
    $build_info = $form_state->getBuildInfo();
    if (!empty($build_info['args'])) {
        $args = array_shift($build_info['args']);
    }
    return $args;
  }

  /**
   * AJAX callback handler for Add Element Form.
   */
  public function modal(&$form, FormStateInterface $form_state){
    $values = $form_state->getValues();
    $errors = array();

    if (!empty($errors)) {
      $form_state->clearErrors();
      drupal_get_messages('error'); // clear next message session;
      $content = '<div class="messages messages--error" aria-label="Error message" role="contentinfo"><div role="alert"><ul>';
      foreach ($errors as $name => $error) {
          $response = new AjaxResponse();
          $content .= "<li>$error</li>";
      }
      $content .= '</ul></div></div>';
      $data = array(
          '#markup' => $content,
      );
      $data['#attached']['library'][] = 'core/drupal.dialog.ajax';
      $data['#attached']['library'][] = 'core/drupal.dialog';
      $response->addCommand(new HtmlCommand('#builder-dialog-messages', $content));
      return $response;
    }
    return $this->dialog($values);
  }

  protected function dialog($values = array()){
    $element = isset($values['element']) ? $values['element'] : array();
    $response = new AjaxResponse();

    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    
    $content['#attached']['library'][] = 'core/drupal.dialog';
    
    $response->addCommand(new CloseDialogCommand('.ui-dialog-content'));

    // quick edit compatible.
    $response->addCommand(new InvokeCommand('.quickedit-toolbar .action-save', 'attr', array('aria-hidden', false)));

    return $response;
    
    }

}