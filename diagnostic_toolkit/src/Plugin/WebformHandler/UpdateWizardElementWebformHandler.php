<?php

namespace Drupal\diagnostic_toolkit\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Update wizard element based on sub element.
 *
 * @WebformHandler(
 *   id = "update_wizard_element",
 *   label = @Translation("Update wizard element based on sub element"),
 *   category = @Translation("Update"),
 *   description = @Translation("Update wizard element based on sub element."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class UpdateWizardElementWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */

  // Function to be fired before submitting the Webform.
  public function preSave(WebformSubmissionInterface $webform_submission, $update = TRUE) { 
    // Get an array of the values from the submission.
    $values = $webform_submission->getData();
    $elements = $webform_submission->getWebform()->getElementsInitialized();
    $input_elements = ['textarea', 'number', 'checkboxes', 'radios', 'webform_likert', 'webform_table', 'webform_entity_select', 'select'];
    
     
      foreach ($elements as $key => $element) {
        $i= 0;
        $j = 0;
        $flag = [];
        $flag_set['fill'] = 0;
        $flag_set['empty'] = 0;
        foreach($element as $index => $item) {
          if(isset($item['#type'])) { 
            if(in_array($item['#type'], $input_elements)) {
             
              if($item['#type'] == 'webform_table') {
                $table_items = $this->checkElementUnderWebformTable($item);
                
                foreach($table_items as $table_item) {
                  $value = $webform_submission->getElementData($table_item['key']); 
                  $flag1[$j] = $this->checkElementDataEmptyorNot($table_item['type'], $value);
                  $j++;
                }
                
                $flag = array_merge($flag, $flag1);
                $i = $i + ($j - 1);
              }
              else {
                $value = $webform_submission->getElementData($index);
                $flag[$i] = $this->checkElementDataEmptyorNot($item['#type'], $value);
              }
              $i++;
            }
          }
        }
        
        $flag_set = array_count_values($flag);
        
        if(isset($flag_set['fill']) && ($flag_set['fill'] == $i)) {
          $webform_submission->setElementData($key . '_elements_status', 2);
        }
        elseif(isset($flag_set['empty']) && $flag_set['empty'] == $i) {
          $webform_submission->setElementData($key . '_elements_status', 0);
        }
        else {
          $webform_submission->setElementData($key . '_elements_status', 1);
        }
      }
      
         
  }

  public function checkElementDataEmptyorNot($type, $value) {
    if(isset($value)) {
      if($type == 'webform_likert') {
        $result = array_filter($value); // Remove empty values
        if(count($result) > 0) {
          $flag = 'fill';
        }else {
          $flag = 'empty';
        }
      }
      else {
        if($value == '' || (is_countable($value) && count($value) == 0)) {
          $flag = 'empty';
        }
        else {
          $flag = 'fill';
        }
      }
    }
    else {
      $flag = 'empty';
    }
    return $flag;
  }

  public function checkElementUnderWebformTable($items) {

    $i = 0;
    foreach($items as $key => $item) {
      $input_elements = ['textarea', 'number', 'checkboxes', 'radios', 'webform_likert', 'webform_table', 'webform_entity_select', 'select'];
      if(is_array($item)) {
        foreach($item as $index => $value) { 
          if(isset($value['#type'])) {
            if(in_array($value['#type'], $input_elements)) {
              $table_items[$i]['type'] = $value['#type'];
              $table_items[$i]['key'] = $index;
              $i++;
            }
          }
        }
      }
    }
    return $table_items;
  }
}

