<?php
namespace Drupal\student_register\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class UpdateForm extends FormBase{

  public function getFormId() {
   return 'register_form';
 }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['student_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Student ID'),
      '#required' => TRUE,
    );
    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name'),
      '#required' => TRUE,
    );
    $form['last_name'] = array (
      '#type' => 'textfield',
      '#title' => t('Last Name'),
      '#required' => TRUE,
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['reset'] = array(
      '#type' => 'submit',
    '#value' => $this->t('Reset'),
    '#validate' => array(),
    '#attributes' => array(
        'onclick' => 'this.form.reset(); return false;',
        ),
      );
    return $form;
  }

 /**
  * {@inheritdoc}
  */
 public function submitForm(array &$form, FormStateInterface $form_state) {
   try{
   $cedula = $form_state->getValue('student_id');
   $firstname = $form_state->getValue('first_name');
   $lastname = $form_state->getValue('last_name');
   $campos = array(
     'cedula' => $cedula,
     'firstname' => $firstname,
     'lastname' => $lastname,
   );
   db_insert('students')
      ->fields($campos)
      ->execute();

   drupal_set_message(t('The information was inserted successfully.'));
 }
//\Exception is now needed for catch errors in D8.
 catch(\Exception $e){
   drupal_set_message(t('Error: %message', array('%message' => $e->getMessage())), 'error');
 }
  }
}
