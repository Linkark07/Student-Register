<?php

namespace Drupal\student_register\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;


class StudentsTable extends ControllerBase {

  /**
   * The Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }


    /**
     * TableSortExampleController constructor.
     *
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    public function __construct(Connection $database) {
      $this->database = $database;
    }

    public function description() {
      global $base_url;


      //$params = \Drupal::routeMatch()->getParameters(NULL, array('q', 'sort', 'order', 'page'));

      //Here we get the parameters that are in the URL.
      $studentid = \Drupal::request()->query->get('studentid');
      $firstname = \Drupal::request()->query->get('firstname');
      $lastname = \Drupal::request()->query->get('lastname');
      $gender = \Drupal::request()->query->get('gender');
      $email = \Drupal::request()->query->get('email');
      $age = \Drupal::request()->query->get('age');


      $enlace = $base_url . '/student/csv/export';
      // We are going to output the results in a table with a header.
      $header = [
        // The header gives the table the information it needs in order to make
        // the query calls for ordering. TableSort uses the field information
        // to know what database column to sort by.
        ['data' => t('Student ID'), 'field' => 's.cedula'],
        ['data' => t('First Name'), 'field' => 's.firstname'],
        ['data' => t('Last Name'), 'field' => 's.lastname'],
        ['data' => t('Registered By'), 'field' => 's.user'],
        ['data' => t('Edit')],
        ['data' => t('Delete')],
      ];

      // Using the TableSort Extender is what tells  the query object that we
      // are sorting.
      $query = $this->database->select('students', 's');
      $query->fields('s', array('cedula', 'firstname', 'lastname', 'user'));

      //Here we confirm if the variable has a value.
     if(isset($studentid))
        {
          $query->condition('s.cedula', "%" . $studentid . "%", 'LIKE');
        }
      if(isset($firstname))
           {
             $query->condition('s.firstname', "%" . $firstname . "%", 'LIKE');
           }
       if(isset($lastname))
            {
              $query->condition('s.lastname', "%" . $lastname . "%", 'LIKE');
            }

        if(isset($gender))
             {
               $query->condition('s.gender', $gender, '=');
             }

         if(isset($email))
              {
                $query->condition('s.email', "%" . $email . "%", 'LIKE');
              }

          if(isset($age))
               {
                 $query->condition('s.age',  $age, '=');
               }


      $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
                    ->orderByHeader($header);
      //We add a Pager Extender with a limit of 10 entries per page.
      $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                        ->limit(10);
      $result = $pager->execute();


      // Don't forget to tell the query object how to find the header information.
      $rows = [];
      foreach ($result as $row) {
        $the_link_path = Url::fromRoute('update.form', ['cedula' => $row->cedula]);
        $link = Link::fromTextAndUrl($this->t('Edit'), $the_link_path);
        $delete_link = Url::fromRoute('delete.form', ['cedula' => $row->cedula]);
        $linkdel = Link::fromTextAndUrl($this->t('Delete'), $delete_link);
        // Normally we would add some nice formatting to our rows
        // but for our purpose we are simply going to add our row
        // to the array.
        $rows[] = array('data' => array(
          'cedula' => $row->cedula,
          'firstname' => $row->firstname,
          'lastname' => $row->lastname,
          'registered' => $row->user,
          'edit' => $link,
          'delete' => $linkdel,
),
        );
      }

      // Build the table for the nice output.
      $build = [
      '#markup' => t('<p><h1>Students Registered</h1></p>
      <p><a target="_blank" href="  @url ">Export Students to CSV</a></p>', array('@url' => $enlace)),
      ];
      $build['tablesort_table'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => "There are no students records to display.",
      ];

      $build['pager'] = array(
     '#type' => 'pager'
      );

      return $build;
    }

    //This function creates a CSV document with all the students registered.
    public function export(){
      header("Content-type: application/vnd.ms-excel");
      header("Content-disposition: csv" . date("Y-m-d") . ".csv");
      header("Content-disposition: filename=student_export.csv");
      $titles = array('Student ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Age');

      $query = $this->database->select('students', 's');
      $query->fields('s', array('cedula', 'firstname', 'lastname', 'gender', 'email', 'age'));
      $result = $query->execute();

      $csv_file = fopen('php://output', 'w') or die('Cant open file!');
      fputcsv($csv_file, $titles);
      foreach($result as $record) {

        fputcsv($csv_file, (array) $record);
        }
        fclose($csv_file);
        die();

    }

}
