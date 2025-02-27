<?php

/*
 * @file
 * Contains \Drupal\ggkp_custom\Controller\NECConferenceController.
 */ 


namespace Drupal\ggkp_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Abraham\TwitterOAuth\TwitterOAuth;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\user\Entity\User;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\tweets\TwitterAPIExchange;
use Drupal\views\Views;


class GgkpCustomController extends ControllerBase {

public function Render($nid,$view_id,$block_id) {
    
    // This is the important part, because will render only the TWIG template.
    //return new Response(render($build));
    $view_arguments=[$nid];
   /* $build['view'] = [
	  '#type' => 'view',
	  '#name' => 'nec_conference_proceedings',
	  '#display_id' => 'block_2',
	  '#arguments' => $view_arguments,     
	];
	* */
	
	$build['view'] = [
	  '#type' => 'view',
	  '#name' => $view_id,
	  '#display_id' => $block_id,
	  '#arguments' => $view_arguments,     
	];
	/*$view =  Views::getView('regional_page_events');
	$style = $view->field;
	if (is_object($view)) {
	\Drupal::logger('regiongefeview_index12')->notice(print_r($style, TRUE));
	}
	$calendar_options = [
		'plugins' => [ 'moment','interaction', 'dayGrid', 'timeGrid', 'list', 'rrule' ],
		'defaultView' => 'dayGridMonth',
		'defaultDate' => $view_arguments,
	  ];
	  $build['#attached']['drupalSettings']['fullCalendarView'][1] = [
		  // The options of the Fullcalendar object.
		  'calendar_options' => json_encode($calendar_options),
	  ];*/
	//$preprocess_service = \Drupal::service('ggkp_custom.fullcalendarview_preprocess');
	//$variables['view'] = $view;
	//$preprocess_service->process($variables);
	
	/*$args = [$tid];
  $view = Views::getView('test_view');
  if (is_object($view)) {
    $view->setArguments($args);
    $view->setDisplay('block');
    $view->preExecute();
    $view->execute();
    $content = $view->buildRenderable('block', $args);
  }
	$build['view'] = [
		'#type' => 'view',
		'#name' => 'regional_page_events',
		'#display_id' => 'block_1',
		'display_options' => ['default' => ['defaultDate' => $view_arguments]],     
	  ];*/
    
$rendered = \Drupal::service('renderer')->renderRoot($build);

$response = new Response();
$response->setContent($rendered);
return $response;
    
  }
  
	
}
