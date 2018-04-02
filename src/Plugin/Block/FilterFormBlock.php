<?php
namespace Drupal\student_register\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
*Provides a form that users can use for filter results
*
*@Block(
*  id = "Filter_Form_BLock",
* admin_label = @Translation("Filter Form Block")
*)
*/
class FilterFormBlock extends BlockBase{
  /**
   * {@inheritdoc}
   */

  protected function blockAccess(AccountInterface $account) {
  if (!$account->isAnonymous()) {
    return AccessResult::allowed();
  }
  return AccessResult::forbidden();
}
   public function build() {
     //This is the replacement to drupal_get_form in D7.
     $form = \Drupal::formBuilder()->getForm('Drupal\student_register\Form\FilterForm');
   return array(
     'block_contribute_form' => $form,
   );
     }


}
