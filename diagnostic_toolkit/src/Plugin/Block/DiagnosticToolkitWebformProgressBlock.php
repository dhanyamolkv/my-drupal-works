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
 *   id = "diagnostictoolkit_webformprogress_block",
 *   admin_label = @Translation("Diagnostic Toolkit Webform Progress Block"),
 * ) 
 */ 

class DiagnosticToolkitWebformProgressBlock extends BlockBase {

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
            ]);
    foreach ($submissions as $key => $submission) {
      $current_page = $submission->get('current_page')->value;
      $submission_data = $submission->getData();
    }

    $progress_bar = '<h4 class="sub_heading">Progress</h4><div class="progress_status"><ul>';
    
    foreach ($elements as $key => $element) {
      
      if($element['#type'] == 'webform_wizard_page') {
        if($key != 'confirmation') {
          if($submission_data['' . $key . '_elements_status'] == 0) {
            $progress_bar .= '<li><div class="item_count"></div><a href="/form/diagnostic-toolkit?page=' . $key . '">' . $element['#title'] . '</a></li>';
          }
          elseif($submission_data['' . $key . '_elements_status'] == 1) {
            $current_page_title = $element['#title'];
            $progress_bar .= '<li class="partially-completed"><div class="item_count"></div><a href="/form/diagnostic-toolkit?page=' . $key . '">' . $element['#title'] . '</a></li>';
          }
          elseif($submission_data['' . $key . '_elements_status'] == 2) {
            $progress_bar .= '<li class="completed"><div class="item_count"></div><a href="/form/diagnostic-toolkit?page=' . $key . '">' . $element['#title'] . '</a></li>';
          }
        }
       
      }
    }
    $progress_bar .= '</ul></div>';
    $progress_bar .= '<div class="continue_btn"><a href="/diagnostic-toolkit-and-sustainable-finance-roadmaps">CONTINUE THE TOOLKIT</a></div>';

    
    return [
        '#markup' => $progress_bar,
        '#allowed_tags' => ['h3', 'select', 'div', 'input', 'p', 'span', 'label', 'table', 'tr', 'th', 'td', 'thead', 'tbody',
        'a', 'section', 'ul', 'li', 'img',
      ],
      ];  
  }
   
  /**
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  } 
  
  
}
 
