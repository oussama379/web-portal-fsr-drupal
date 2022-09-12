<?php
namespace Drupal\gavias_blockbuilder\Form;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
class DelForm extends ConfirmFormBase  {
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
      return 'del_form';
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
    return parent::buildForm($form, $form_state);
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
    $response = new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal::url('gavias_blockbuilder.admin'));
    $response->send();
  }

}