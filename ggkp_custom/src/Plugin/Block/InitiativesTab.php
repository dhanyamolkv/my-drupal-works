<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use \Drupal\Core\Url;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "initiatives_tab",
 *   admin_label = @Translation("Initiatives Tab Block."),
 * )
 */
class InitiativesTab extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => block_show_tab(),
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
function block_show_tab() {
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
      if (is_numeric($path[2])) {
        $node = \Drupal\node\Entity\Node::load($path[2]);
      }
      else {
        $alias = substr($current_path, 0, strrpos( $current_path, '/'));
        //$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/etapes-de-la-vie');

        $params = Url::fromUri("internal:" . $alias)->getRouteParameters();
        $entity_type = key($params);
        $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
      }      
    }
    if($node != '') {
      $output = '<ul>';
      static $i = 0;
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
      
      /*if($node_array['field_show_about_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-about-wrapper"><a href="' . $path_alias . '/about">'.$node_array['field_about_tab_title']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_regional_pro']['0']['value'] == '1'){
          $output .= '<li class="initiative-regions-wrapper"><a href="' . $path_alias . '/regions">'.$node_array['field_region_l']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_activities_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-activities-wrapper"><a href="' . $path_alias . '/activities">'.$node_array['field_activities_tab_title']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_partners_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-partners-wrapper"><a href="' . $path_alias . '/partners">'.$node_array['field_partners_tab_title']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_resource_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-resource-wrapper"><a href="' . $path_alias . '/knowledge">'.$node_array['field_resource_tab_title']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_blogs_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-blogs-wrapper"><a href="' . $path_alias . '/blogs">'.$node_array['field_blogs_tab_title']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_leaders_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-leaders-wrapper"><a href="' . $path_alias . '/leaders">'.$node_array['field_leaders_tab_title']['0']['value'].'</a></li>';
      }    
      if($node_array['field_show_events_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-events-wrapper"><a href="' . $path_alias . '/events">'.$node_array['field_events_tab_title']['0']['value'].'</a></li>';
      }
      if($node_array['field_show_contact_us_tab']['0']['value'] == '1'){
          $output .= '<li class="initiative-contact-us-wrapper"><a href="' . $path_alias . '/contact">'.$node_array['field_contact_us_tab_title']['0']['value'].'</a></li>';
      }*/
      $output .= '</ul>';
      return $output;
    }
}
