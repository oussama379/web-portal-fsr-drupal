<?php
namespace Drupal\gavias_blockbuilder\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class AddFormPopup extends FormBase{

  /**
   * {@inheritdoc}.
   */
  public function getFormId(){
    return 'add_form_popup';
  }

  /**
   * {@inheritdoc}.
  */
  public function buildForm(array $form, FormStateInterface $form_state){
  if(\Drupal::request()->attributes->get('random')) $random = \Drupal::request()->attributes->get('random');
    $args = $this->getFormArgs($form_state);
    
    $form['builder-dialog-messages'] = array(
      '#markup' => '<div id="builder-dialog-messages"></div>',
    );

    $form['random'] = array(
      '#type' => 'hidden',
      '#default_value' => $random
    );

    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => 'Title',
      '#default_value' => $bblock['title']
    );
     $form['body_class'] = array(
      '#type' => 'textfield',
      '#title' => 'Machine name',
      '#description' => 'A unique machine-readable name containing letters, numbers, and underscores<br>Sample home_page_1, Use shortcode for page basic [gbb name="home_page_1"]',
      '#default_value' => $bblock['body_class']
    );

    $form['actions'] = array(
      '#type' => 'action',
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
    if (!$form_state->getValue('title')  ) {
      $form_state->setErrorByName('title', 'Please enter title for buider block.');
    }
    if (!$form_state->getValue('body_class') ) {
      $form_state->setErrorByName('title', ('Please enter Machine name for buider block.'));
    }
    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $form_state->getValue('body_class'))){
      $form_state->setErrorByName('title', ('The machine-readable name must contain only lowercase letters, numbers, and underscores.'));
    }
    if(gavias_blockbuilder_check_machine($form_state->getValue('id'), $form_state->getValue('body_class'))){
      $form_state->setErrorByName('title', ('Machine name for buider block exits.'));
    }
  }

  /**
   * {@inheritdoc}
   * Submit handle for adding Element
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    $errors = array();

    if (!$form_state->getValue('title')  ) {
        $errors[] ='Please enter title for buider block.';
      }
    if (!$form_state->getValue('body_class') ) {
      $errors[] = ('Please enter Machine name for buider block.');
    }
    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $form_state->getValue('body_class'))){
      $errors[] = ('The machine-readable name must contain only lowercase letters, numbers, and underscores.');
    }

    $errors_exist = '';
    if(gavias_blockbuilder_check_machine(0, $form_state->getValue('body_class'))){
      $errors[] = ('Machine name for buider block exits.');
      $errors_exist = true;
    }

    if($errors){

    }else{
      $values = $form_state->getValues();
      $pid = db_insert("gavias_blockbuilder")
        ->fields(array(
          'title'       => $form['title']['#value'],
          'body_class'  => $form['body_class']['#value'],
          'params'      => '',
        ))
        ->execute();
    }

    $form_state->setValue('pid', $pid);
    $form_state->setValue('body_class', $form['body_class']['#value']);
    $form_state->setValue('errors_exist', $errors_exist);
    $form_state->setValue('errors', $errors);
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
   
    if (!$form_state->getValue('title')  ) {
        $errors[] ='Please enter title for buider block.';
      }
    if (!$form_state->getValue('body_class') ) {
      $errors[] = ('Please enter Machine name for buider block.');
    }
    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $form_state->getValue('body_class'))){
      $errors[] = ('The machine-readable name must contain only lowercase letters, numbers, and underscores.');
    }
    
    if($form_state->getValue('errors_exist')){
      $errors[] = ('Machine name for buider block exits.');
    }

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

    $pid = $values['pid'];
    $title = $values['title'];
    $body_class = $values['body_class'];
    $random = $values['random'];
    $element = isset($values['element']) ? $values['element'] : array();
    $response = new AjaxResponse();

    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    
    $content['#attached']['library'][] = 'core/drupal.dialog';
    
    $response->addCommand(new CloseDialogCommand('.ui-dialog-content'));
    
    $response->addCommand(new InvokeCommand('.field--type-blockbuilder .gva-choose-gbb.gva-id-'.$random . ' span', 'removeClass', array('active')));

    $html = '';
    $html .= '<span class="gbb-item active id-'.$pid.'">';
    $html .= '<a class="select" data-id="'.$pid.'" title="'. $body_class .'">' . $title  . '</a>';
    $html .= ' <span class="action">( <a class="edit gva-popup-iframe" href="'.\Drupal::url('gavias_blockbuilder.admin.edit', array('bid'=>$pid)).'" title="'. $body_class .'">Edit</a>';
    $html .= ' | <a>Please save and refesh if you want duplicate</a>) </span>';
    $html .= '</span>';

    $response->addCommand(new InvokeCommand('.field--type-blockbuilder .gva-choose-gbb', 'append', array($html)));
    
    $response->addCommand(new InvokeCommand('.field_block_builder.gva-id-'.$random, 'val', array($pid)));

    // quick edit compatible.
    $response->addCommand(new InvokeCommand('.quickedit-toolbar .action-save', 'attr', array('aria-hidden', false)));

    $response->setAttachments($content['#attached']);

    return $response;
    }

}