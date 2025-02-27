<?php

namespace Drupal\ggkp_custom\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Link;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

class GGKPCustomBreadcrumbBuilder implements BreadcrumbBuilderInterface {
   /**
    * {@inheritdoc}
    */
   public function applies(RouteMatchInterface $attributes) { 
      $parameters = $attributes->getParameters()->all();
      $route_name = \Drupal::routeMatch()->getRouteName(); 
      
      if($route_name == "entity.node.canonical") {
        if (!empty($parameters['node'])) {
          if($parameters['node']->bundle() == 'country') {
            return TRUE;
          }
        }
      }
     if ($route_name == "view.sme_detail_page.page_1") {
       
        if ($attributes->getParameter('facets_query')) {
          return TRUE;
        }
      }
      return FALSE;
   }

   /**
    * {@inheritdoc}
    */
   public function build(RouteMatchInterface $route_match) {  
      $current_path = \Drupal::service('path.current')->getPath(); 
      $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
      $path = explode("/", $result);    
    //$args = explode('/', $current_path);  
      if ($current_path) {
        $args = explode("/", $current_path);
      }
      if ($path[1] == "country") {
        if(isset($args[2]) && $args[2] != '') {
          $nid = $args[2]; 
          $node = Node::load($nid); 
          //$region_id = $node->get('field_regions')->target_id; 
          $region_id = $node->field_regions->target_id;
          if(isset($region_id)) {
            $region_name = \Drupal\taxonomy\Entity\Term::load($region_id)->get('name')->value;
          }

          $request = \Drupal::request();
          $route_match = \Drupal::routeMatch();
          $title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
                  
          $breadcrumb = new Breadcrumb();
          $breadcrumb->addLink(Link::createFromRoute('Home', '<front>'));
          $breadcrumb->addLink(Link::createFromRoute('Country Data', 'view.regions_related.page_1'));
          if(isset($region_id)) {
        	  $breadcrumb->addLink(Link::createFromRoute($region_name, 'entity.taxonomy_term.canonical', ['taxonomy_term' => $region_id]));
          }
          $breadcrumb->addLink(Link::createFromRoute($title, '<none>'));

          
        }
    }
   if ($path[1] == 'sme-operations-support-centre' && $path[2] == 'browse') {
      $breadcrumb = new Breadcrumb();
      $term_pretty_path = '';
      $breadcrumb->addLink(Link::createFromRoute('SME Support Centre', 'empty_page.page_5'));
      
      $pattern = "/field_sme_operations_support_cen/";
      $pregvalue = preg_match($pattern, $current_path); 
      if ($pregvalue == 1) { 
        $path1 = explode("/field_sme_operations_support_cen/", $current_path);
        foreach($path1 as $value) {
          $path2 = explode("/", $value);
          if(!empty($path2[0])) {
            $termvalue = $path2[0]; 
            $termValue = explode("-", $termvalue); 
            $termid = end($termValue);  
            // load taxonomy parents from termid
            $parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($termid);
            $parent = reset($parent);
            if($parent) {
              $sec_parent_tid = $parent->id();
              $first_parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($sec_parent_tid);
              $first_parent = reset($first_parent);
              $first_parent_id = $first_parent->id();  
      
              $term_name = \Drupal\taxonomy\Entity\Term::load($first_parent_id)->get('name')->value;
              if(isset($term_name)) {
                $breadcrumb->addLink(Link::createFromRoute($term_name, '<none>'));
              }
              
            }
            $route_object = $route_match->getRouteObject();
            $route_without_facets_query = explode('/{facets_query}', $route_object->getPath())[0];
            
            $term_name = \Drupal\taxonomy\Entity\Term::load($termid)->get('name')->value;
            $term_name_path = \Drupal::service('pathauto.alias_cleaner')->cleanString($term_name) . '-' . $termid;
            
            if($term_pretty_path == '') {
              $term_pretty_path = '/field_sme_operations_support_cen/' . $term_name_path;
            }
            else {
              $term_pretty_path = $term_pretty_path . '/field_sme_operations_support_cen/' . $term_name_path;              
            }
            
            $url = Url::fromUserInput($route_without_facets_query . $term_pretty_path);
            $breadcrumb->addLink(Link::fromTextAndUrl($term_name, $url));
            
          }
        }
               
        $links = $breadcrumb->getLinks();
      }
    }
    
    $breadcrumb->addCacheContexts(['route', 'url.path', 'languages']);
    return $breadcrumb;
    
   }
  }

