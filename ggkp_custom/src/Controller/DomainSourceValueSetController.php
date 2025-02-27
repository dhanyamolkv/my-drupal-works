<?php

namespace Drupal\ggkp_custom\Controller;

use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * GGKP Domain Source Value Set.
  * Utility: Set domain access to domain source if single value.
 */
class DomainSourceValueSetController {

  public function index(){
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'indicators', '!=')
      ->condition('field_domain_access', NULL, 'IS NOT NULL')
      ->condition('field_domain_source', NULL, 'IS NULL')
      ->execute();
    foreach ($nids as $nid) {
      $data['nid'] = $nid;
      $operations[] = [
        '\Drupal\ggkp_custom\Controller\DomainSourceValueSetController::ggkp_custom_update_nodes_process_batch',
        [$data],
      ];
    }
    if (count($operations)) {
      $batch = [
        'title' => t('Source set in progress...'),
        'operations' => $operations,
        'finished' => '\Drupal\ggkp_custom\Controller\DomainSourceValueSetController::ggkp_custom_update_nodes_finished',
      ];
      batch_set($batch);
      return batch_process('/dashboard');
    }
    else {
      $messenger = \Drupal::service("messenger");
      $messenger->addStatus(t('No data found!!!'));
    }

    return [
      '#markup' => 'Complete',
    ];
  }

  /**
   * Batch processing callback for each batch of nodes.
   */
  public static function ggkp_custom_update_nodes_process_batch($data, &$context) {
    $nid = $data['nid'];
    $node = Node::load($nid);
    if ($node) {
      $domain_access = $node->field_domain_access->getValue();
      $domain_source = $node->field_domain_source->getValue();
      if (count($domain_source) == 0 && count($domain_access) == 1) {
        $node->set('field_domain_source', $domain_access);
        $node->save();
      }
    }
  }

  /**
   * Batch processing finished callback.
   */
  public static function ggkp_custom_update_nodes_finished($success, $results, $operations) {
    $messenger = \Drupal::service("messenger");
    if ($success) {
      $messenger->addStatus('Nodes updated successfully.');
    }
    else {
      $messenger->addStatus('An error occurred while updating nodes.');
    }
  }

}