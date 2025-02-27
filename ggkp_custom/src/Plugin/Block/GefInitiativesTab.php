<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "gef_initiatives_tab",
 *   admin_label = @Translation("GEF Initiatives Tab Block."),
 * )
 */
class GefInitiativesTab extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => block_gef_show_tab1(),
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
function block_gef_show_tab1() {
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
        $path = explode("/", $current_path);
        $node = \Drupal\node\Entity\Node::load($path[2]);
      }
      if($node != '') {
        $output = '<ul>';
        
        $node_array = $node->toArray();
        $path_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node_array['nid']['0']['value']);
  
        $paragraph = $node->field_content_section->getValue();
      // Loop through the result set.
      foreach ( $paragraph as $element ) {
        $p = \Drupal\paragraphs\Entity\Paragraph::load($element['target_id']);
        $text = $p->field_menu_title->getValue()[0]['value'];  
        $show_menu = $p->field_show_as_menu_tab->getValue()[0]['value'];
        $menu_position = $p->field_menu_position->getValue()[0]['value']; 
        if($show_menu == '1'){
            $menus[$i]['title'] = $text;
            $menus[$i]['path'] = $path_alias . '/' . $text;
            $menus[$i]['weight'] = $menu_position;
            $i++;
        }
      }

  
    if($node_array['field_show_regional_pro']['0']['value'] == '1'){
      $menus[$i]['title'] = $node_array['field_region_l']['0']['value'];
      $menus[$i]['path'] = $path_alias . '/regions';
      $menus[$i]['weight'] = $node_array['field_region_tab_position']['0']['value'];
      $i++;
    }
    if($node_array['field_show_partners_tab']['0']['value'] == '1'){
      $menus[$i]['title'] = $node_array['field_partners_tab_title']['0']['value'];
      $menus[$i]['path'] = $path_alias . '/partners';
      $menus[$i]['weight'] = $node_array['field_partners_tab_position']['0']['value'];
      $i++;
    }
    if($node_array['field_show_resource_tab']['0']['value'] == '1'){
      $menus[$i]['title'] = $node_array['field_resource_tab_title']['0']['value'];
      $menus[$i]['path'] = $path_alias . '/knowledge';
      $menus[$i]['weight'] = $node_array['field_resource_tab_position']['0']['value'];
      $i++;
    }
    if($node_array['field_show_blogs_tab']['0']['value'] == '1'){
      $menus[$i]['title'] = $node_array['field_blogs_tab_title']['0']['value'];
      /*if($node_array['nid']['0']['value'] == 84317) {
        $menus[$i]['path'] = $path_alias . '/News';
      }
      else {*/
        $menus[$i]['path'] = $path_alias . '/News';
      //}
      $menus[$i]['weight'] = $node_array['field_blogs_tab_position']['0']['value'];
      $i++;
    }
    if($node_array['field_show_leaders_tab']['0']['value'] == '1'){
      $menus[$i]['title'] = $node_array['field_leaders_tab_title']['0']['value'];
      $menus[$i]['path'] = $path_alias . '/leaders';
      $menus[$i]['weight'] = $node_array['field_leaders_tab_position']['0']['value'];
      $i++;
    }   
    if($node_array['field_show_events_tab']['0']['value'] == '1'){
      $menus[$i]['title'] = $node_array['field_events_tab_title']['0']['value'];
      $menus[$i]['path'] = $path_alias . '/events';
      $menus[$i]['weight'] = $node_array['field_events_tab_position']['0']['value'];
      $i++;
    }
    
    if(isset($menus) && is_array($menus)) {
      $key_values = array_column($menus, 'weight'); 
      array_multisort($key_values, SORT_ASC, $menus);
      foreach ( $menus as $key => $menu ) {
        $output .= '<li class="initiative-menu-wrapper"><a href="' . $menu['path'] .'">'.$menu['title'].'</a></li>';
        
      }
    }
      
      $output .= '</ul>';
      return $output;
    }
}
