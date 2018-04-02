<?php
namespace Drupal\student_register\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;



class RegisterForm extends FormBase{

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
    $form['student_gender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#required' => TRUE,
      '#options' => array(
        'Female' => t('Female'),
        'Male' => t('Male'),
      ),
    );
    $form['student_email'] = array (
      '#type' => 'email',
      '#title' => t('Email'),
      '#required' => TRUE,
    );
    $form['student_age'] = array (
      '#type' => 'textfield',
      '#title' => t('Age'),
      '#required' => TRUE,
    );
    /*$form['candidate_dob'] = array (
      '#type' => 'date',
      '#title' => t('DOB'),
      '#required' => TRUE,
    );
    $form['candidate_confirmation'] = array (
      '#type' => 'radios',
      '#title' => ('Are you above 18 years old?'),
      '#options' => array(
        'Yes' =>t('Yes'),
        'No' =>t('No')
      ),
    );
    $form['candidate_copy'] = array(
      '#type' => 'checkbox',
      '#title' => t('Send me a copy of the application.'),
    );*/
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
  //Since Age is requiered, we won't check if the field is empty.
  public function validateForm(array &$form, FormStateInterface $form_state){
    if (!intval($form_state->getValue('student_age'))) {
                $form_state->setErrorByName('student_age', $this->t('Age needs to be a number'));
               }
  parent::validateForm($form, $form_state);
  }

 /**
  * {@inheritdoc}
  */
 public function submitForm(array &$form, FormStateInterface $form_state) {
   $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
   try{
   $cedula = $form_state->getValue('student_id');
   $firstname = $form_state->getValue('first_name');
   $lastname = $form_state->getValue('last_name');
   $gender = $form_state->getValue('student_gender');
   $email = $form_state->getValue('student_email');
   $age = $form_state->getValue('student_age');
   $usuario = $user->get('name')->value;

   $campos = array(
     'cedula' => $cedula,
     'firstname' => $firstname,
     'lastname' => $lastname,
     'gender' => $gender,
     'email' => $email,
     'age' => $age,
     'user' => $usuario,
   );
   db_insert('students')
      ->fields($campos)
      ->execute();

   drupal_set_message(t('The student was registered successfully.'));
   $this->logger('student_register')->notice('Inserted student with ID: %cedula', array('%cedula' => $cedula));
 }
//Exception is now needed for catch errors in D8.
 catch(\Exception $e){
   drupal_set_message(t('Error: %message', array('%message' => $e->getMessage())), 'error');
   $this->logger('student_register')->error('Error: %message', array('%message' => $e->getMessage()));
 }
//ksm($query);

  }
}
