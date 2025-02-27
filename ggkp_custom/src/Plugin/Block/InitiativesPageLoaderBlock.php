<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "show_initiatives_loader",
 *   admin_label = @Translation("Show Initiatives Loader Block."),
 * )
 */
class InitiativesPageLoaderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => block_show_loading(),
      '#allowed_tags' => ['h3', 'select', 'div', 'input', 'p', 'span', 'label', 'table', 'tr', 'th', 'td', 'thead', 'tbody',
        'a', 'section', 'ul', 'li', 'img', 'option',
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
function block_show_loading() {
    $output = '<div class="initiative_show_loader"></div>';
    return $output;
}
