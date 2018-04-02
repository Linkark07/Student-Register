<?php
namespace Drupal\student_register\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
Use Drupal\Core\Routing;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilterForm extends FormBase{

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
   return 'filter_form';
 }
 public function buildForm(array $form, FormStateInterface $form_state){

   $params = \Drupal::routeMatch()->getParameters(NULL, array('q', 'sort', 'order', 'page'));

   $form['search'] = array
   	(
   		'#type' => 'details',
   		'#title' => t('Filters'),
   		'#open' => TRUE,
   		// We only want the fieldset collapsed if values have not been
   		// submitted for the search form. If no values have been submitted,
   		// $params will be an empty array, and the fieldset will be
   		// collapsed
   		//'#collapsed' => !count($params),
   	);

     $form['search']['datos_generales'] = array(
       '#markup' => '<p><b>Input the data you want to find</b></p>'
     );

     $form['search']['studentid'] = array(
       '#title' => t('Student ID'),
       '#type' => 'textfield',
       // Here we get from the url the value from that variable.
       '#default_value' => \Drupal::request()->query->get('studentid'),
       '#prefix' => '<tr><td>',
       '#suffix' => '</td>',

     );

     $form['search']['firstname'] = array(
       '#title' => t('First Name'),
       '#type' => 'textfield',
       '#default_value' => \Drupal::request()->query->get('firstname'),
       '#prefix' => '<tr><td>',
       '#suffix' => '</td>',

     );

     $form['search']['lastname'] = array(
       '#title' => t('Last Name'),
       '#type' => 'textfield',
       '#default_value' => \Drupal::request()->query->get('lastname'),
       '#prefix' => '<tr><td>',
       '#suffix' => '</td>',

     );

     $form['search']['gender'] = array (
       '#type' => 'select',
       '#title' => ('Gender'),
       '#default_value' => \Drupal::request()->query->get('gender'),
       '#options' => array(
         'all' => t('All'),
         'Male' => t('Male'),
         'Female' => t('Female'),
       ),
     );

     $form['search']['email'] = array(
       '#title' => t('Email'),
       '#type' => 'textfield',
       '#default_value' => \Drupal::request()->query->get('email'),
       '#prefix' => '<tr><td>',
       '#suffix' => '</td>',

     );


     $form['search']['age'] = array(
       '#title' => t('Age'),
       '#type' => 'textfield',
       '#default_value' => \Drupal::request()->query->get('age'),
       '#prefix' => '<tr><td>',
       '#suffix' => '</td>',

     );

     $form['search']['buttons'] = array
     	(
     		'#type' => 'actions',
     	);

     $form['search']['save']['filter_results'] = array(
       '#type' => 'submit',
       '#value' => $this->t('Search'),
       '#button_type' => 'primary',
     );

     $form['search']['buttons']['reset_filters'] = array
   	(
    /*  '#type' => 'submit',
    '#value' => $this->t('Reset'),
    '#validate' => array(),
    '#attributes' => array(
        'onclick' => 'this.form.reset(); return false;',
      ),*/
      '#type' => 'submit',
      //'#submit' => array('::previousForm'), //this works too
      '#submit' => array([$this, 'resetForm']),
      '#value' => 'Reset',
      '#limit_validation_errors' => array(), //no validation for back button

   	);

   return $form;

   }
   //We must check also that the field is empty, otherwhise, the validation error will activate.
   public function validateForm(array &$form, FormStateInterface $form_state){
     if (!intval($form_state->getValue('age')) && $form_state->getValue('age') != NULL) {
                 $form_state->setErrorByName('age', $this->t('Age needs to be a number'));
                }
   parent::validateForm($form, $form_state);
 }

  //We create an array that gets all the filters we introduced.
   public function submitForm(array &$form, FormStateInterface $form_state) {
     $params = [];
     try{
  //We have to check if the string is empty. If it isn't, we add the value to the array.
     if(strlen($form_state->getValue('studentid')))
       {
         $params['studentid'] = $form_state->getValue('studentid');
       }
     if(strlen($form_state->getValue('firstname')))
     	{
        $params['firstname'] = $form_state->getValue('firstname');
     	}
      if(strlen($form_state->getValue('lastname')))
       {
         $params['lastname'] = $form_state->getValue('lastname');
       }
     if($form_state->getValue('gender') != 'all')
     {
       $params['gender'] = $form_state->getValue('gender');
     }
     if(strlen($form_state->getValue('email')))
     	{
     		$params['email'] = $form_state->getValue('email');
     	}
     if(strlen($form_state->getValue('age')))
     	{
     		$params['age'] = $form_state->getValue('age');
     	}

//  We redirect to the student table with the parameters.
     $form_state->setRedirect('students_table', array($params));


  /*  $form_state->setRedirect('students_table', array(
      'studentid' => $studentid,
      'firstname' => $firstname,
    ));*/
   }
   catch(\Exception $e){
     drupal_set_message(t('Error: %message', array('%message' => $e->getMessage())), 'error');
    $this->logger('student_register')->error('Error: %message', array('%message' => $e->getMessage()));
   }

     }
     //In this function, we redirect to the table without any parameter in the url.
     public static function resetForm(array &$form, FormStateInterface $form_state) {
    $url = Url::fromRoute('students_table');
    return $form_state->setRedirectUrl($url);
}

   }
