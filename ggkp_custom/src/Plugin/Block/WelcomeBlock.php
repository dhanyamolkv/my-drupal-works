<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\user\Entity\User;
use Drupal\field\Entity\FieldConfig;

/**
 * Provides a 'Welcome user' Block.
 *
 * @Block(
 *   id = "welcomeuser_block",
 *   admin_label = @Translation("Welcome user block"),
 *   category = @Translation("Welcome User"),
 * )
 */
class WelcomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    $roles = $user->getRoles();
    $admin_roles = array('ggkp_admin', 'administrator', 'ggkp_admin_level_2');
    if (in_array('administrator', $roles) || in_array('ggkp_admin', $roles) || in_array('initiatives_manager', $roles) || in_array('external_user_level2', $roles)
      || in_array('external_content_creator', $roles))  {
      $account = \Drupal\user\Entity\User::load($uid); // pass your uid
      if (in_array('external_content_creator', $roles)) {
	    $first_name = $account->get('field_first_name_dt')->value;
        $last_name = $account->get('field_last_name_dt')->value;
        $name = $first_name . ' ' . $last_name;
	  }
	  else {
	    $name = $account->get('name')->value;
	  }
	  $user_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/user/'.$uid);
    }
    $add_resource_link = " <a href= '/manage-anonymous-contents'>Add your Resource</a>";
    $dashboard_link = " <a href= '/dashboard'>Dashboard</a>";
    if(count(array_intersect($roles, $admin_roles)) == 0) {
      if(in_array('initiatives_manager', $roles)) {
        $dashboard_link = "<a href= '/initiative-manager-dashboard'>Dashboard</a>";
      }
      if(in_array('external_user_level2', $roles)) {
        $dashboard_link = "<a href= '/manage-external-user-dashboard'>Dashboard</a>";
      }
      if(in_array('external_content_creator', $roles)) {
        $dashboard_link = "<a href= '/manage-anonymous-contents'>Add your Resource</a>";
        //$dashboard_link = '';
      }
    }
    $profName = "<span class='welcome-text'>Hello </span> <a href= '" . $user_alias ."'>" . $name . "</a>";

      $userloginblock = "<div class='welcome-block'>
        <div class='welcome-user'>" . " " . $profName . "</div>
        <div class='dashboard-link'>" . " " . $dashboard_link . "</div>
   	
      </div>";

    return [
      '#markup' => $userloginblock,
    ];
  }

  /**
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
