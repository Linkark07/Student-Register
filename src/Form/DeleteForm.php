<?php

namespace Drupal\student_register\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\ConfirmFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a confirmation form for deleting mymodule data.
 */
class DeleteForm extends ConfirmFormBase {

  /**
   * The ID of the item to delete.
   *
   * @var string
   */
  protected $cedula;

  /**
   * {@inheritdoc}
   */

   public function buildForm(array $form, FormStateInterface $form_state, $cedula = NULL) {
     //We pass here the value of the student ID that we want to delete.
     $this->cedula = $cedula;
     return parent::buildForm($form, $form_state);
   }

   /**
    * {@inheritdoc}
    */
   public function submitForm(array &$form, FormStateInterface $form_state) {
     $cedula = $this->cedula;
     try{
     db_delete('students')
       ->condition('cedula', $cedula, '=')
       ->execute();

       drupal_set_message('Student was deleted from the database');
       $this->logger('student_register')->notice('Deleted student with ID: %cedula', array('%cedula' => $this->cedula));

       $form_state->setRedirectUrl($this->getCancelUrl());

   }
   catch(\Exception $e){
     drupal_set_message(t('db_update failed. Message = %message, query= %query', [
       '%message' => $e->getMessage(),
       '%query' => $e->query_string,
     ]
     ), 'error');
     $form_state->setRedirectUrl($this->getCancelUrl());
   }
 }

  public function getFormId() {
    return 'delete_form';
  }

  /**
   * {@inheritdoc}
   */

   //In this section we create the confirmation view that appears when an user selects delete.
  public function getQuestion() {
    return t('Do you want to delete %cedula?', array('%cedula' => $this->cedula));
  }

  /**
   * {@inheritdoc}
   */
    public function getCancelUrl() {
      return new Url('students_table');
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
    return t('Nevermind');
  }

  /**
   * {@inheritdoc}
   *
   * @param int $id
   *   (optional) The ID of the item to be deleted.
   */

}
