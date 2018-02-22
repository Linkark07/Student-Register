<?php
namespace Drupal\student_register\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;


class UpdateForm extends FormBase{


  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }
    /**
     *
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    public function __construct(Connection $database) {
      $this->database = $database;
    }

  public function getFormId() {
   return 'update_form';
 }

  public function buildForm(array $form, FormStateInterface $form_state, $cedula = NULL) {

    $query = $this->database->select('students', 's');
    $query->fields('s', array('cedula', 'firstname', 'lastname', 'gender', 'email', 'age'));
    $query->condition('s.cedula', $cedula, '=');
    $result = $query->execute();

    while ($row = $result->fetchAssoc()) {

    $form['student_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Student ID'),
      '#default_value' => $row['cedula'],
      '#required' => TRUE,
      '#attributes' => array('readonly' => 'readonly'),
    );
    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name'),
      '#default_value' => $row['firstname'],
      '#required' => TRUE,
    );
    $form['last_name'] = array (
      '#type' => 'textfield',
      '#title' => t('Last Name'),
      '#default_value' => $row['lastname'],
      '#required' => TRUE,
    );
    $form['student_gender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#required' => TRUE,
      '#default_value' => $row['gender'],
      '#options' => array(
        'Female' => t('Female'),
        'male' => t('Male'),
      ),
    );
    $form['student_email'] = array (
      '#type' => 'email',
      '#title' => t('Email'),
      '#default_value' => $row['email'],
      '#required' => TRUE,
    );
    $form['student_age'] = array (
      '#type' => 'textfield',
      '#title' => t('Age'),
      '#default_value' => $row['age'],
      '#required' => TRUE,
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Update'),
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
    }
    return $form;
  }

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
   try{

   $cedula = $form_state->getValue('student_id');
   $campos = array(
     'firstname' => $form_state->getValue('first_name'),
     'lastname' => $form_state->getValue('last_name'),
     'gender' => $form_state->getValue('student_gender'),
     'email' => $form_state->getValue('student_email'),
     'age' => $form_state->getValue('student_age'),
   );
   /*$query = $this->database->update('students');
   $query->fields($campos);
   $query->condition('cedula', $cedula, '=');
   $query->execute();
   */

     db_update('students')
      ->fields($campos)
      ->condition('cedula', $cedula, '=')
      ->execute();

   drupal_set_message(t('The information was updated successfully.'));

 }
//\Exception is now needed for catch errors in D8.
 catch(\Exception $e){
   drupal_set_message(t('db_update failed. Message = %message, query= %query', [
     '%message' => $e->getMessage(),
     '%query' => $e->query_string,
   ]
   ), 'error');
 }
//ksm($query);

  }
}
