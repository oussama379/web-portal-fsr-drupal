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
class DelFormPopup extends FormBase{

  /**
   * The ID of the item to delete.
   *
   * @var string
   */
    protected $bid;

   /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
   public function getFormID() {
      return 'del_form_popup';
   }
  
  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you want to delete %bid?', array('%bid' => $this->bid));
  }

  /**
   * {@inheritdoc}
   */
    public function getCancelUrl() {
      return new Url('gavias_blockbuilder.admin');
  }

  /**
   * {@inheritdoc}
   */
    public function getDescription() {
    return t('Only do this if you are sure!');
  }

  /**
   * {@inheritdoc}
   */
    public function getConfirmText() {
    return t('Delete it!');
  }

  /**
   * {@inheritdoc}
   */
    public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   *
   * @param int $id
   *   (optional) The ID of the item to be deleted.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $bid = NULL) {
    $this->bid = $bid;
    if(\Drupal::request()->attributes->get('bid')) $bid = \Drupal::request()->attributes->get('bid');
    if(\Drupal::request()->attributes->get('random')) $random = \Drupal::request()->attributes->get('random');
    $form['builder-dialog-messages'] = array(
      '#markup' => '<div id="builder-dialog-messages">'.t('Do you want to delete it') .'</div>',
    );
    $form['id'] = array(
      '#type' => 'hidden',
      '#default_value' => $bid
    );
    $form['random'] = array(
      '#type' => 'hidden',
      '#default_value' => $random
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
     $bid = $this->bid;
    if(!$bid && \Drupal::request()->attributes->get('bid')) $bid = \Drupal::request()->attributes->get('bid');
    db_delete('gavias_blockbuilder')
            ->condition('id', $bid)
            ->execute();
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    drupal_set_message("blockbuilder '#{$bid}' has been delete");
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
    $pid = $values['id'];
    $random = $values['random'];
    $element = isset($values['element']) ? $values['element'] : array();
    $response = new AjaxResponse();

    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    
    $content['#attached']['library'][] = 'core/drupal.dialog';
    
    $response->addCommand(new CloseDialogCommand('.ui-dialog-content'));

    $response->addCommand(new InvokeCommand('.field--type-blockbuilder .gva-choose-gbb .gbb-item.id-' . $pid, 'remove'));

    $response->addCommand(new InvokeCommand(".field--type-blockbuilder .gva-choose-gbb.gva-id-{$random} .gbb-item.disable", 'addClass', array('active')));

    $response->addCommand(new InvokeCommand('.field_block_builder.gva-id-'.$random, 'val', array('')));

    // quick edit compatible.
    $response->addCommand(new InvokeCommand('.quickedit-toolbar .action-save', 'attr', array('aria-hidden', false)));

    return $response;
    
    }

}