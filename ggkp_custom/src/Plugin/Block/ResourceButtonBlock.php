<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "resource_button_block",
 *   admin_label = @Translation("Resource Button Block"),
 * )
 */
class ResourceButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => get_buttons(),
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
function get_buttons() {

  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node instanceof \Drupal\node\NodeInterface) {
    // You can get nid and anything else you need from the node object.
    $nid = $node->id();
  }

  $output = '<p>
            <a href="/resource-usage?resource_id=' . $nid . '">Tell us how you used this resource</a> 
            <a href="/contact/Publication-suggestions-for-the-resource-library/">Suggest a green growth resource</a>
            </p>';

  return $output;
}
