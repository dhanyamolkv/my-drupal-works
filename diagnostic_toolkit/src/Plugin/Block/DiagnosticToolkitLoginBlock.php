<?php

namespace Drupal\diagnostic_toolkit\Plugin\block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\user\Entity\User;
use Drupal\field\Entity\FieldConfig;

/**
 * Provides a 'Login user' Block.
 * 
 * @Block(
 *   id = "diagnostictoolkit_login_block",
 *   admin_label = @Translation("Diagnostic Toolkit Login Block"),
 * ) 
 */ 

class DiagnosticToolkitLoginBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */ 
  public function build() {
    if (\Drupal::currentUser()->isAnonymous()) {	  
	  $regBtn = "<a href='/user/register/diagnostic_toolkit'>REGISTER</a>";
	  $loginBtn = "<a href='/user/diagnostic-toolkit/login'>LOGIN</a>";
	  $text1 = "<div class='dt-no-account'>Don’t have an account?</div>";
	  $text2 = "<div class='dt-account'>Already registered?</div>";
	  $regLoginsection = "<div class='dt-reg-login-block'>
	    <div class='left'>$text1 $regBtn</div>
	    <div class='right'>$text2 $loginBtn</div>
	    </div>";
	}
	else {	  
	  $regLoginsection = "<div class='dt-webform-section'><a href='/diagnostic-toolkit-and-sustainable-finance-roadmaps'>TO THE TOOLKIT</a></div>";
	}
	return [
      '#markup' => $regLoginsection,
    ];  
  }
   
  /**
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }  
}
 
