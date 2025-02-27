<?php

namespace Drupal\diagnostic_toolkit\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\user\Entity\User;
use Drupal\field\Entity\FieldConfig;

/**
 * Provides a 'Welcome user' Block.
 *
 * @Block(
 *   id = "diagnostictoolkit_Welcome_block",
 *   admin_label = @Translation("Diagnostic Toolkit Welcome Block"),
 * )
 */
class DiagnosticToolkitWelcomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
     
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    $roles = $user->getRoles();    
    //if (in_array('diagnostic_toolkit', $roles))  {
      $account = \Drupal\user\Entity\User::load($uid); // pass your uid
      $first_name = $account->get('field_first_name_dt')->value;
      $last_name = $account->get('field_last_name_dt')->value;
      $full_name = $first_name . ' ' . $last_name;
      /*if (!$account->user_picture->isEmpty()) {
        $pictureUri = $account->user_picture->entity->getFileUri();
        $style = \Drupal::entityTypeManager()->getStorage('image_style')->load('profile_image_small');
        $urlPicture = $style->buildUrl($pictureUri);
      }
      else {
        $picture = // get default picture
      }*/
    //}
	  
      //$profName = "<span class='welcome-text'>Welcome </span> <a href= '/users/" . $name ."'>" . $full_name . "</a>";
      $profName = "<span class='welcome-text'>Welcome </span> <a href= '/user/" . $uid ."/edit'>" . $full_name . "</a>";
      $dashboard_link = " <a href= '/dashboard'>Dashboard</a>";
      $welcomeblockcontent = "<div class='welcome-block'>";
     /* if($urlPicture != '') {
        $welcomeblockcontent .= "<div class='welcome-user-picture'>" . " <img src='" . $urlPicture . "'></div>";
      } */
      $welcomeblockcontent .= "<div class='welcome-user'>" . " " . $profName . "</div>
      <a href='/user/logout?destination=diagnostic-toolkit'>Logout</a>
      </div>";
        
    return [
      '#markup' => $welcomeblockcontent,
    ];
  }

  /**
   * Cache Age.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
