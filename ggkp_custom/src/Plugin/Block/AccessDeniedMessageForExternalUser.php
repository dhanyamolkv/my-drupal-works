<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "access_denied_message_for_external_user",
 *   admin_label = @Translation("Access Denied Message For External User"),
 * )
 */
class AccessDeniedMessageForExternalUser extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => get_access_denied_message(),
      '#allowed_tags' => ['h3', 'select', 'div', 'input', 'p', 'span', 'label', 'table', 'tr', 'th', 'td', 'thead', 'tbody',
        'a', 'section', 'ul', 'li', 'img', 'a',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}

/**
 * Custom function reset button.
 */
function get_access_denied_message() {

  /*$request = \Drupal::request();
  $referer = $request->headers->get('referer');
  if($referer != '') {
  
    $referred_domain = explode('/', $referer); 
    $domain = \Drupal::entityTypeManager()->getStorage('domain')->load('ggkp_main_domain');
    $domain_url = $domain->getPath();
    $domain_host = $domain->getHostName();*/
    
  
    //if($referred_domain[2] == $domain_host) {
      $output = '<p>
      You are already logged in to the Knowledge Partner’s page. To upload knowledge, please click on \'Add your Resource\' in the top right corner of your screen.
                </p>';
    //}
  //}

  return $output;
}
