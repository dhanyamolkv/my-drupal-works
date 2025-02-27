<?php

namespace Drupal\ggkp_custom\Controller;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Drupal\Core\Url;
use Drupal\path_alias\AliasStorageInterface;

/**
 * Set a Initiative to nodes.
  * Utility: Set a Initiative to nodes.
 */
class SetInitiativeController {

  public function setInitiative($initiative_id) {
    $initiative_node = Node::load($initiative_id);
    if ($initiative_node && $initiative_node->getType() == 'initiatives') {
      $entity_type_manager = \Drupal::service('entity_type.manager');
      $path = drupal_get_path('theme', 'ggkp');
      $fileName = $path . '/assets/data/PotentialResourcesForCriticalMinOnGGKP.xlsx';
      $spreadsheet = IOFactory::load($fileName);
      $sheetData = $spreadsheet->getActiveSheet();
      $highestRow = $sheetData->getHighestRow();
      $highestColumn = $sheetData->getHighestColumn();
      $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
      // Loop through rows and columns to fetch data
      for ($row = 1; $row <= $highestRow; $row++) {
        $cells = [];
        for ($column = 1; $column <= $highestColumnIndex; $column++) {
          // Get the cell value.
          $cellValue = $sheetData->getCellByColumnAndRow($column, $row)->getValue();
          // Do something with the cell value.
          $cells[] = trim($cellValue);
        }
        $rows[] = $cells;
      }
      $offset = 1;
      $content_rows = array_slice($rows, $offset);
      $node_updated = [];
      $i = 0;
      $operations = [];
      foreach ($content_rows as $row) {
        if (!empty($row[5]) && !empty($row[6]) && $row[6] === 'Yes') {
          $operations[] = [
            '\Drupal\ggkp_custom\Controller\SetInitiativeController::ggkp_custom_set_initiative_process_batch',
            [$row, $initiative_id],
          ];
          $i++;
        }
      }
      if (count($operations)) {
        $batch = [
          'title' => t('Set in progress...'),
          'operations' => $operations,
          'finished' => '\Drupal\ggkp_custom\Controller\SetInitiativeController::ggkp_custom_set_initiative_finished',
        ];
        batch_set($batch);
        return batch_process('/dashboard');
      }
      return [
        '#markup' => 'Changed successfully...',
      ];
    }
    return [
      '#markup' => 'Invalid URL.',
    ];
  }

  /**
   * Batch processing callback for each batch of row.
   */
  public static function ggkp_custom_set_initiative_process_batch($row, $initiative_id, &$context) {
    if (!empty($row[5])) {
      $path_alias = $row[5];
      $path_alias_repository = \Drupal::service('path_alias.repository');
      if ($path_alias_repository->lookupByAlias($path_alias, 'en')) {
        $path = \Drupal::service('path_alias.manager')->getPathByAlias($path_alias);
        if (!is_array($path)) {
          $url = Url::fromUri('internal:' . $path);
          $parameters = $url->getRouteParameters();
          if (!empty($parameters['node'])) {
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($parameters['node']);
            if ($node) {
              $initiatives = [];
              $initiatives = $node->field_initiatives_content->getValue();
              $initiatives[] = $initiative_id;
              $node->set('field_initiatives_content', $initiatives);
              $node->save();
            }
          }
        }
      }
    }
  }

  /**
   * Batch processing finished callback.
   */
  public static function ggkp_custom_set_initiative_finished($success, $results, $operations) {
    $messenger = \Drupal::service("messenger");
    if ($success) {
      $messenger->addStatus('Set successfully.');
    }
    else {
      $messenger->addStatus('An error occurred while updating nodes.');
    }
  }

}
