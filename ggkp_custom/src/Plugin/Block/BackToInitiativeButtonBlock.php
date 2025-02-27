<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "Back_to_initiative_button_block",
 *   admin_label = @Translation("Back To Initiative Button Block"),
 * )
 */
class BackToInitiativeButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => get_back_buttons(),
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
function get_back_buttons() {

  $request = \Drupal::request();
    $referer = $request->headers->get('referer');
    $session = $request->getSession();
    // Getting the base url.
    $base_url = Request::createFromGlobals()->getSchemeAndHttpHost();

    // Getting the alias or the relative path.
    if ($referer !== null) {
      $alias = substr($referer, strlen($base_url));
      $path = explode('/', $alias);
    }

  $output = '<p>
            <a href="' . $referer . '">Back To Initiative</a> 
            </p>';

  return $output;
}
