<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "event_view_listing_block",
 *   admin_label = @Translation("Events Listing Type"),
 * )
 */
class EventViewListing extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => block_toggle_btn(),
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
function block_toggle_btn() {
  global $base_url;
  $current_path = \Drupal::service('path.current')->getPath();
  $url = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
  
  $month = date('m');
  $year =  date('Y');

  $output = '<div class="event-list-calendar">';

  if ($url == "/events" || $url == "/events/past" || $url == "/events/ggkp-annual-conferences") {
    $output .= '<div class="event-list active"><a href="/events">List view</a></div>
  
    <div class="event-calendar"><a href="/event-calendar/' . $year . $month . '">Calendar view</a></div>';
  }
  else {
    $output .= '<div class="event-list"><a href="/events">List view</a></div>
  
    <div class="event-calendar active"><a href="/event-calendar/' . $year . $month . '">Calendar view</a></div>';
  }
  
  $output .= '</div>';

  return $output;
}
