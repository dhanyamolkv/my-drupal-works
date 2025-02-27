<?php

namespace Drupal\ggkp_custom\Controller;

use Drupal\node\Entity\Node;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\taxonomy\Entity\Term;

/**
 * GEF Shorts Contents.
 * Utility: Assign multimedia contents to shorts.
 * @param $termid
 * field organisation content ID
 */
class GEFShortsController {

  public function index() {
    $termid = 10990; // Replace with your actual term ID.
    $nids = [
      450723,
      450722,
    ];

    // Load the term to which you want to associate these nodes.
    $nodes = Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      // Set the new content type for the node.
      $node->set('type', 'short');

      // Save the updated node.
      $node->save();
    }
    
    return [
      '#type' => 'markup',
      '#markup' => "Content has been changed to Shorts.",
    ];
  }

}