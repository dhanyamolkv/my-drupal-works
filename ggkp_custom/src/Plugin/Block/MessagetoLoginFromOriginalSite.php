<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "message_to_login_from_original_site",
 *   admin_label = @Translation("Message to login from original site"),
 * )
 */
class MessagetoLoginFromOriginalSite extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => get_login_message(),
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
function get_login_message() {

  $request = \Drupal::request();
  $referer = $request->headers->get('referer');
  if($referer != '') {
  
    $referred_domain = explode('/', $referer); 
    $domain = \Drupal::entityTypeManager()->getStorage('domain')->load('ggkp_main_domain');
    $domain_url = $domain->getPath();
    $domain_host = $domain->getHostName();
    
  
    if($referred_domain[2] == $domain_host) {
      $output = '<p>
                  Please login using your ggkp.org credentials(username/password), you will be redirected to Dashboard. From the dashboard you can manage your content.
                </p>';
    }
  }

  return $output;
}
