<?php

namespace Drupal\ggkp_custom\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\taxonomy;
use \Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity;

/**
 * Redirect .html pages to corresponding Node page.
 */
class RequestHandler implements EventSubscriberInterface {

  /**
   * Redirect code.
   *
   * @var int*/
  private $redirectCode = 301;

  /**
   * Redirect pattern based url.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Event.
   */
  public function customRedirection(GetResponseEvent $event) {

    $request    = \Drupal::request();
    $requestUrl = $request->server->get('REQUEST_URI', NULL);

    /*
     * redirecting search user page
     */
    $current_path = \Drupal::service('path.current')->getPath();

    if ($current_path) {
      $path = explode("/", $current_path);
    }
    if (($path[1] == "event-calendar") && isset($path[2])  && str_contains($path[2], '-')) {
      $url = '';
      if(isset($path[2])  && str_contains($path[2], '-')) {
        $yrmonth = str_replace('-', '', $path[2]);
        $url = '/event-calendar/' . $yrmonth;
        $response = new RedirectResponse($url);
        if (isset($response)) {
          $response->send();
          exit(0);
        }
      }
    }
    if ($current_path == '/contact/Publication-suggestions-for-the-resource-library/Suggested%20Publication%20for%20the%20%20Resource%20Library') {
      $url = '';
      //if(isset($path[3])  && ($path[3] == 'Suggested%20Publication%20for%20the%20%20Resource%20Library')) {
        //$yrmonth = str_replace('-', '', $path[2]);
      global $base_url;
        $url = $base_url . '/' . $path[1] . '/' . $path[2] . '?subject=' . $path[3];
        $response = new RedirectResponse($url);
        if (isset($response)) {
          $response->send();
          exit(0);
       // }
      }
    }
    
    /* Redirection for Key search term (tags) in node detail page */
    if ($path[1] == 'taxonomy' && $path[2] == 'term' && is_numeric($path[3])) {
      if(!isset($path[4])) {
        $url = '';
        global $base_url;
        $tag_id = $path[3];		
        $termName = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tag_id);
        if (isset($termName) && $termName != '') {
              $tag_term_name = \Drupal\taxonomy\Entity\Term::load($tag_id)->get('name')->value;
          $term = \Drupal\taxonomy\Entity\Term::load($tag_id);
            $vid = $term->bundle();
            if (isset($vid) && $vid == 'tags') {
              $loader = \Drupal::service('domain.negotiator');
              $current_domain = $loader->getActiveDomain();
              $current_domain_id = $current_domain->id();
              switch ($current_domain_id) {
                case 'gef_islands_domain':
                    $url = $base_url . '/gef/search/?keyword=' . $tag_term_name;
                    break;
                case 'spar6c':
                    $url = $base_url . '/spar6c/search?keyword=' . $tag_term_name;
                    break;
                case 'ggkp_main_domain':
                    $url = $base_url . '/search/global-site';
                    break;
                default:
                    $url = $base_url . '/search/site?keyword=' . $tag_term_name;
                break;
              }
              $response = new RedirectResponse($url);
              if (isset($response)) {
              $response->send();
              exit(0);
              }
            }
            if (isset($vid) && $vid == 'organisation') {
            $loader = \Drupal::service('domain.negotiator');
            $current_domain = $loader->getActiveDomain();
            $current_domain_id = $current_domain->id();
            switch ($current_domain_id) {
              case 'gef_islands_domain':
                  $url = $base_url . '/knowledge';
                  break;
              case 'spar6c':
                  $url = $base_url . '/knowledge-products';
                  break;
              case 'ggkp_main_domain':
                  $url = $base_url;
                  break;
              default:
                  $url = $base_url . '/knowledge/browse';
              break;
            }
            $response = new RedirectResponse($url);
            if (isset($response)) {
                $response->send();
                exit(0);
            }
          }
        } 
        else { 
          $response = new RedirectResponse($base_url);
          if (isset($response)) {
              $response->send();
              exit(0);
          }
        } 
      }  
	  }
   
    if($path[1] == 'user' && is_numeric($path[2])) {
      $request = \Drupal::request();
      $referer = $request->headers->get('referer');
      if($referer != '') {      
        $referred_domain = explode('/', $referer); 
        $domain = \Drupal::entityTypeManager()->getStorage('domain')->load('ggkp_main_domain');
        $domain_url = $domain->getPath();
        $domain_host = $domain->getHostName();      
        if($referred_domain[2] == $domain_host) {
          $response = new RedirectResponse('/dashboard');
          if (isset($response)) {
            $response->send();
            exit(0);
          }
        }
      }
    }
    if($path[1] == 'node' && is_numeric($path[2])) { 
      $node = \Drupal::request()->attributes->get('node');
      if ($node) {
        // Now you can safely call bundle() on $node.
        $typeName = $node->bundle();
      }
      if($typeName == 'upload_data') {
        $publish = $node->get('status')->value;
        if (isset($publish) && $publish == '1') {
          $knowledge_resources_id = $node->get('field_knowledge_resource_id')->value;
          $response = new RedirectResponse('/node/'. $knowledge_resources_id .'/edit');
          if (isset($response)) {
            $response->send();
            exit(0);
          }
        }
      }
    } 
  }

  /**
   * Listen to kernel request events and call custom redirection.
   *
   * {@inheritdoc}.
   *
   * @return array
   *   Event names to listen to (key) and methods to call (value)
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = ['customRedirection'];
    return $events;
  }
}
