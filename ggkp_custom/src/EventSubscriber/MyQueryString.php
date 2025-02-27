<?php

namespace Drupal\ggkp_custom\Plugin\facets\url_processor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\facets\Plugin\facets\url_processor\QueryString;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Query string URL processor.
 *
 * @FacetsUrlProcessor(
 *   id = "my_query_string",
 *   label = @Translation("My Query string"),
 *   description = @Translation("Add description")
 * )
 */
class MyQueryString extends QueryString {

  /**
   * A string of how to represent the facet in the url.
   *
   * @var string
   */
  protected $urlAlias;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Request $request, EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $eventDispatcher) {
    //this is where I'm adding a default value
    $this->alterRequest($request);
    parent::__construct($configuration, $plugin_id, $plugin_definition, $request, $entity_type_manager, $eventDispatcher);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  private function alterRequest(Request $request): void {
    $query = $request->query;
    $facet_new_parameters = [];

    $current_path = \Drupal::service('path.current')->getPath();
    /*if() {
      if ($facet_parameters = $query->get('f')) {
        $content_type_filter_exists = FALSE;

        foreach ($facet_parameters as $parameter) {
          $sepator = ':';
          $explosion = explode($sepator, $parameter);
          $facet_type = array_shift($explosion);
          if ($facet_type === 'content_type') {
            $content_type_filter_exists = TRUE;
          }
        }

        if (!$content_type_filter_exists) {
          $facet_new_parameters = array_merge($facet_parameters, ['content_type:prodotto']);
        }
      }
      else {
        $facet_new_parameters = ['content_type:prodotto'];
      }
    }*/

    if (!empty($facet_new_parameters)) {
      $query->set('f', $facet_new_parameters);
    }
  }
}