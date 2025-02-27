<?php

namespace Drupal\diagnostic_toolkit\Plugin\block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\user\Entity\User;
use Drupal\field\Entity\FieldConfig;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'Login user' Block.
 * 
 * @Block(
 *   id = "diagnostictoolkit_webformpage_status_block",
 *   admin_label = @Translation("Diagnostic Toolkit Webform Page Status Block"),
 * ) 
 */ 

class DiagnosticToolkitWebformPageStatusBlock extends BlockBase {

    /**
   * The webform submission.
   *
   * @var \Drupal\webform\WebformSubmissionInterface
   */
  protected $webform_submission;

  /**
   * {@inheritdoc}
   */ 
  public function build() {
    $webform_id = 'diagnostic_toolkit';
    $webform = Webform::load($webform_id);
    $elements = $webform->getElementsDecodedAndFlattened();
    $uid = \Drupal::currentUser()->id();
  
    $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties([
              'uid' => $uid,
              'webform_id' => 'diagnostic_toolkit',
              //'in_draft' => TRUE,
            ]);
    foreach ($submissions as $key => $submission) {
      $submission_data = $submission->getData();
    }
    
    $i = 1;
    foreach ($elements as $key => $element) {
      
      
      if($element['#type'] == 'webform_wizard_page') {
        
        if(isset($submission_data['' . $key . '_elements_status'])) {
          if($submission_data['' . $key . '_elements_status'] == 1) {
            $current_page_title = $element['#title'];
            $dt_webform_page_status['partially_completed'][] = $i;
          }
          elseif($submission_data['' . $key . '_elements_status'] == 2) {
            $dt_webform_page_status['completed'][] = $i;

          }
        }
        $i++;
      }
    }
    $completed_pages = implode(',', $dt_webform_page_status['completed']);
    if(isset($dt_webform_page_status['partially_completed'])) {
      $partially_completed_pages = implode(',', $dt_webform_page_status['partially_completed']);
    }
       
    $progress_bar = '<h4 class="main_heading">Confirmation Screen</h4><div class="progress_status">';
    if($completed_pages != '') {
      $progress_bar .= '<p>You have completed steps '. $completed_pages .' (colored green). ';
    }
    if($partially_completed_pages != '') {
      $progress_bar .= 'You have the remaining questions in steps '. $partially_completed_pages .' (colored grey). '; 
    }
    $progress_bar .= 'Review the answers you submitted below.</p>
    <p>Only completed steps will feed into a downloadable “National Sustainable Finance Status Report”. 
    Please go back to partially completed steps by clicking them on the progress bar and finalize your answers.</p>
    ';
    

    
    return [
        '#markup' => $progress_bar,
      ];  
  }
   
  /**
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }   
  
}
 
