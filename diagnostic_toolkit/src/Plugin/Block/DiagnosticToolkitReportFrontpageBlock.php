<?php

namespace Drupal\diagnostic_toolkit\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\field\Entity\FieldConfig;

/**
 * Provides a 'ReportFrontPage user' Block.
 *
 * @Block(
 *   id = "diagnostictoolkit_ReportFrontPage_block",
 *   admin_label = @Translation("Diagnostic Toolkit Report FrontPage Block"),
 * )
 */
class DiagnosticToolkitReportFrontpageBlock extends BlockBase {
/*
 * {@inheritdoc}
 */
  public function build() {
    $uid = \Drupal::currentUser()->id();
    if (isset($uid) && $uid != '') {
      $account = \Drupal\user\Entity\User::load($uid);
      $country = $account->get('field_country_dt')->referencedEntities()[0]->label();
      $reportfrontpagecontent = "<div class='dt-report'>";
      if (isset($country) && $country != '') {
	    $reportfrontpagecontent .= "<div class='dt-country'>".$country."</div>";
      }
      "</div>";
      return [
        '#markup' => $reportfrontpagecontent,
      ];
    }	  
  }
  /*
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }     	
} 
