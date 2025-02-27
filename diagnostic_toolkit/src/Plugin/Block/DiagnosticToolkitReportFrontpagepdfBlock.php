<?php

namespace Drupal\diagnostic_toolkit\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\field\Entity\FieldConfig;

/**
 * Provides a 'ReportFrontPagepdf user' Block.
 *
 * @Block(
 *   id = "diagnostictoolkit_ReportFrontPagepdf_block",
 *   admin_label = @Translation("Diagnostic Toolkit Report FrontPagepdf Block"),
 * )
 */
class DiagnosticToolkitReportFrontpagepdfBlock extends BlockBase {
/*
 * {@inheritdoc}
 */
  public function build() {
    $uid = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($uid);
    $fcrs_logo = '/themes/custom/webform_survey/assets/images/dt-pdf/fc4s_logo.jpg';
    $ggkp_logo = '/themes/custom/webform_survey/assets/images/dt-pdf/GGKP_Finance_Platform_Colour.jpg';

    $current_date = date("d/M/Y");
    $reportpdf = "<div class='dt-pdf'>";
    $reportpdf .= "<img class ='dt-logo1' src=". $fcrs_logo . ">";
    $reportpdf .= "<img class ='dt-logo2'  src=" . $ggkp_logo . ">";   
    $reportpdf .= "<div class='dt-pdf-date'>".$current_date."</div>";
    "</div>";
    
    return [
      '#markup' => $reportpdf,
    ];	  
  }
  /*
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
