<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "gef_initiatives_tab_ip",
 *   admin_label = @Translation("GEF Initiatives Tab Block GIP."),
 * )
 */
class GefGIPInitiativesTab extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => block_gef_show_tab_ip(),
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
function block_gef_show_tab_ip() {
  $route_name = \Drupal::routeMatch()->getRouteName();
  if ($route_name == "entity.taxonomy_term.canonical") {
    $current_path = \Drupal::service('path.current')->getPath();
    $path = explode("/", $current_path);
    $term = Term::load($path[3]);
    $vocabularyId = $term->bundle(); 
    if($vocabularyId == 'regional_programmes') {
      $node_id = 84317;  
      $node = \Drupal\node\Entity\Node::load($node_id);
    }
  }
  else {
    $current_path = \Drupal::service('path.current')->getPath();
    $path = explode("/",$current_path);
    $node = \Drupal\node\Entity\Node::load($path[2]);
  }
  if($node != '') {
    $node_array = $node->toArray();
    $output = '<ul>';
    if($node_array['field_show_about_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-about-wrapper">'.$node_array['field_about_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_regional_pro']['0']['value'] == '1'){
        $output .= '<li class="initiative-regions-wrapper">'.$node_array['field_region_l']['0']['value'].'</li>';
    }
    if($node_array['field_show_activities_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-activities-wrapper">'.$node_array['field_activities_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_partners_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-partners-wrapper">'.$node_array['field_partners_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_resource_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-resource-wrapper">'.$node_array['field_resource_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_blogs_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-news-wrapper">'.$node_array['field_blogs_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_leaders_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-leaders-wrapper">'.$node_array['field_leaders_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_events_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-events-wrapper">'.$node_array['field_events_tab_title']['0']['value'].'</li>';
    }
    if($node_array['field_show_contact_us_tab']['0']['value'] == '1'){
        $output .= '<li class="initiative-contact-us-wrapper">'.$node_array['field_contact_us_tab_title']['0']['value'].'</li>';
    }
    $output .= '</ul>';
    return $output;
  }
}
