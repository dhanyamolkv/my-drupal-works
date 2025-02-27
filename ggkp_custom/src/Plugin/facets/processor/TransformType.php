<?php

namespace Drupal\ggkp_custom\Plugin\facets\processor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;

/**
 * Provides a processor for TransformType.
 *
 * @FacetsProcessor(
 *   id = "transform_type",
 *   label = @Translation("TransformType"),
 *   description = @Translation("Transform type labels"),
 *   stages = {
 *     "build" = 35
 *   }
 * )
 */
 
/* Change the Facet label of Course in Type Facet to Training in SME detail page in GGIP */ 
class TransformType extends ProcessorPluginBase implements BuildProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet, array $results) {
    $config = $this->getConfiguration();

    /** @var \Drupal\facets\Result\Result $result */
    foreach ($results as $result) {
      if ($result->getRawValue() == 'courses') {
        $result->setDisplayValue("Training");
      }
    }
    return $results;
  }
}
