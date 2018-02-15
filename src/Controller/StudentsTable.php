<?php

namespace Drupal\student_register\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;


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

    public function custom_l($text, $the_link_path) {
      $link_generator = \Drupal::service('link_generator');
      $installer_url = \Drupal\Core\Url::fromUri('base://'. $the_link_path );
      $link = $link_generator->generate($text, $installer_url);
      return $link;
    }

    public function description() {
      // We are going to output the results in a table with a nice header.
      $header = [
        // The header gives the table the information it needs in order to make
        // the query calls for ordering. TableSort uses the field information
        // to know what database column to sort by.
        ['data' => t('Student ID'), 'field' => 's.cedula'],
        ['data' => t('First Name'), 'field' => 's.firstname'],
        ['data' => t('Last Name'), 'field' => 's.lastname'],
        ['data' => t('Operations')],
      ];

      // Using the TableSort Extender is what tells  the query object that we
      // are sorting.
      $query = $this->database->select('students', 's');
      //  ->extend('Drupal\Core\Database\Query\TableSortExtender');
      $query->fields('s', array('cedula', 'firstname', 'lastname'));
      $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
                    ->orderByHeader($header);

      $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                        ->limit(10);
      $result = $pager->execute();


      // Don't forget to tell the query object how to find the header information.
    //  $result = $query
    //    ->orderByHeader($header)
    //    ->execute();

      $rows = [];
      foreach ($result as $row) {
        $the_link_path = 'student/update/' . $row->cedula;
      //  $link = l(t('Borrar'), 'man/borrar/' . $row->cedula);
        // Normally we would add some nice formatting to our rows
        // but for our purpose we are simply going to add our row
        // to the array.
        $rows[] = array('data' => array(
          'cedula' => $row->cedula,
          'firstname' => $row->firstname,
          'lastname' => $row->lastname,
          'edit' => $the_link_path,
        ));
      }

      // Build the table for the nice output.
      $build = [
        '#markup' => '<p>' . t('Students Registered.') . '</p>',
      ];
      $build['tablesort_table'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => "There aren't values in the table",
      ];
      $build['pager'] = array(
     '#type' => 'pager'
      );

      return $build;
    }

}
