<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\student_register\Controller;
class StudentController {
  public function content() {
    $output = array(
      'third_para' => array(
          '#type' => 'markup',
          '#markup' => t('<p>The information you have requested is provided solely for purposes related to the delegation of Domain Names and the operation of the DNS administered by NIC-Panama.</p>
           <p>It is absolutely forbidden to use it for other purposes, including the sending of unsolicited e-mail for advertising or promotion of products and services (spam) without the authorization of the affected and NIC-Panama.</p>
           <p>The database generated from the system is protected by the laws of Intellectual Property and all international treaties on the matter.</p>
           <p><h3> If you need more information of the information shown here, please contact nic@nic.pa </ h3></p>'
        ),
      ),

        'fourth_para' => array(
          '#type' => 'markup',
          '#markup' => t('<p>Return to the <a href="  @base_path ">main page</a></p>', array('@base_path' => base_path())),
        ),
    );

    return $output;
  }

  public function help(){
    $output = array(

      'first_para' => array(
        '#type' => 'markup',
        '#markup' => t('This is an example'),

      ),
    );
    return $output;

  }
}
