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
 * Split Tools and Platforms.
  * Utility: Split Tools and Platforms.
 */
class SplitToolsAndPlatformsController {

  public function ggkp_custom_duplicate_content_type() {

    // Get the entity type manager.
    $entity_type_manager = \Drupal::service('entity_type.manager');

    $originalContentTypeId = 'tools_and_platforms';
    $newContentTypeId = 'platforms';

    // Check if the new content type already exists.
    if ($entity_type_manager->getStorage('node_type')->load($newContentTypeId)) {
      return [
        '#markup' => $newContentTypeId. ' already exists',
      ];
    }

    // Get the original content type.
    $originalContentType = $entity_type_manager->getStorage('node_type')->load($originalContentTypeId);

    if (!$originalContentType) {
      \Drupal::logger('SplitToolsAndPlatforms')->error('Original content type @id not found', ['@id' => $originalContentTypeId]);
      return [
        '#markup' => 'Error: Original content type not found',
      ];
    }

    // Create a new content type.
    $newContentType = $originalContentType->createDuplicate();

    // Update the machine name and label of the new content type.
    $newContentType->set('type', $newContentTypeId);
    $langcode = 'en';
    $newContentType->set('langcode', $langcode);
    $newContentType->set('label', 'Platforms', $langcode);

    // Save the new content type.
    try {
      $newContentType->save();
    } catch (EntityStorageException $e) {
      \Drupal::logger('SplitToolsAndPlatforms')->error('Error saving new content type: @message', ['@message' => $e->getMessage()]);
      return [
        '#markup' => 'Error: Unable to save new content type',
      ];
    }

    // Update the label of the original content type.
    $originalContentType->set('langcode', $langcode);
    $originalContentType->set('label', 'Tools', $langcode);

    // Save the original content type.
    $originalContentType->save();

    // Clear the cache to reflect the changes.
    \Drupal::entityTypeManager()->getStorage('node_type')->resetCache();

    return [
      '#markup' => $originalContentTypeId. ' cloned to '. $newContentTypeId,
    ];

  }

  public static function change_node_content_type() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $path = drupal_get_path('theme', 'ggkp');
    $fileName = $path . '/assets/data/Tools_and_platforms_separation_2023.xlsx';
    $spreadsheet = IOFactory::load($fileName);
    $sheetData = $spreadsheet->getActiveSheet();
    // Get the range of rows that contain data
    $activeRange = $sheetData->getAutoFilter()->getRange();
    $activeRows = explode(':', $activeRange)[1];
    $rangeEnd = Coordinate::coordinateFromString($activeRows);
    $highestRow = $rangeEnd[1];
    $highestColumnIndex = Coordinate::columnIndexFromString($rangeEnd[0]);

    // Loop through rows and columns to fetch data
    for ($row = 1; $row <= $highestRow; $row++) {
      $cells = [];
      for ($column = 1; $column <= $highestColumnIndex; $column++) {
          //Get the cell value
          $cellValue = $sheetData->getCellByColumnAndRow($column, $row)->getValue();
          // Do something with the cell value
          $cells[] = trim($cellValue);
          // $cells[] = $column;
      }
      $rows[] = $cells;
    }
    $offset = 1;
    $content_rows = array_slice($rows, $offset);
    $node_updated = [];
    $i = 0;
    foreach ($content_rows as $row) {
      if ($row[14] == 'Platform' || $row[15] == 'delete') {
        $operations[] = [
          '\Drupal\ggkp_custom\Controller\SplitToolsAndPlatformsController::ggkp_custom_split_content_type_process_batch',
          [$row],
        ];
        $i++;
      }
    }
    if (count($operations)) {
      $batch = [
        'title' => t('Split in progress...'),
        'operations' => $operations,
        'finished' => '\Drupal\ggkp_custom\Controller\SplitToolsAndPlatformsController::ggkp_custom_split_content_type_finished',
      ];
      batch_set($batch);
      return batch_process('/dashboard');
    }
    return [
      '#markup' => 'Changed successfully...',
    ];
  }

  /**
   * Batch processing callback for each batch of row.
   */
  public static function ggkp_custom_split_content_type_process_batch($row, &$context) {
    $node = FALSE;
    if (!empty($row[12])) {
      $path_alias = $row[12];
      $path_alias_repository = \Drupal::service('path_alias.repository');
      if ($path_alias_repository->lookupByAlias($path_alias, 'en')) {
        $path = \Drupal::service('path_alias.manager')->getPathByAlias($path_alias);
        if (!is_array($path)) {
          $url = Url::fromUri('internal:' . $path);
          $parameters = $url->getRouteParameters();
          if (!empty($parameters['node'])) {
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($parameters['node']);
          }
        }
      }
    }
    if ($node) {
      if ($row[14] == 'Platform') {
        // Set the new content type machine name.
        $new_content_type = 'platforms';
        // Update the node's content type.
        $node->set('type', $new_content_type);
        // Set the new URL alias.
        $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $node->id());
        $split_alias = explode('/',$alias);
        $new_alias = '/platforms/'. $split_alias[2];
        $node->path->alias = $new_alias;
        //Save the changes.
        $node->save();
        $node_updated[] = 'Node ID:'. $node->id() .' changed to platform';
      }
      elseif ($row[15] == 'delete') {
        $node->delete();
        $node_updated[] = 'Node ID:'. $node->id() .' deleted';
      }
    }
  }

  /**
   * Batch processing finished callback.
   */
  public static function ggkp_custom_split_content_type_finished($success, $results, $operations) {
    $messenger = \Drupal::service("messenger");
    if ($success) {
      $messenger->addStatus('Splitted successfully.');
    }
    else {
      $messenger->addStatus('An error occurred while updating nodes.');
    }
  }

  public static function change_other_node_content_type() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $path = drupal_get_path('theme', 'ggkp');
    $fileName = $path . '/assets/data/Tools_and_platforms_separation_2023.xlsx';
    $spreadsheet = IOFactory::load($fileName);
    $sheetData = $spreadsheet->getActiveSheet();
    // Get the range of rows that contain data
    $activeRange = $sheetData->getAutoFilter()->getRange();
    $activeRows = explode(':', $activeRange)[1];
    $rangeEnd = Coordinate::coordinateFromString($activeRows);
    $highestRow = $rangeEnd[1];
    $highestColumnIndex = Coordinate::columnIndexFromString($rangeEnd[0]);

    // Loop through rows and columns to fetch data
    for ($row = 1; $row <= $highestRow; $row++) {
      $cells = [];
      for ($column = 1; $column <= $highestColumnIndex; $column++) {
          //Get the cell value
          $cellValue = $sheetData->getCellByColumnAndRow($column, $row)->getValue();
          // Do something with the cell value
          $cells[] = trim($cellValue);
          // $cells[] = $column;
      }
      $rows[] = $cells;
    }
    $offset = 1;
    $content_rows = array_slice($rows, $offset);
    $node_updated = [];
    $i = 0;
    $filtered_rows = array_filter($content_rows, function($row){
      $str = strtolower($row[15]);
      // Check 'change to' is present and exclude tools & platform.
      return strpos($str, 'change to ') === 0 && !in_array(substr($str, 10), ['tools', 'platform']);

    });
    foreach ($filtered_rows as $row) {
      $node = FALSE;
      if (!empty($row[12])) {
        $path_alias = $row[12];
        $path_alias_repository = \Drupal::service('path_alias.repository');
        if ($path_alias_repository->lookupByAlias($path_alias, 'en')) {
          $path = \Drupal::service('path_alias.manager')->getPathByAlias($path_alias);
          if (!is_array($path)) {
            $url = Url::fromUri('internal:' . $path);
            $parameters = $url->getRouteParameters();
            if (!empty($parameters['node'])) {
              $node = \Drupal::entityTypeManager()->getStorage('node')->load($parameters['node']);
            }
          }
        }
      }
      if ($node) {
        // Set the new content type machine name.
        $str = strtolower($row[15]);
        // remove 'change to'.
        $new_content_type = substr($str, 10);
        //$new_content_type = 'platforms';
        // Update the node's content type.
        $node->set('type', $new_content_type);
        // Set the new URL alias.
        $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $node->id());
        $split_alias = explode('/',$alias);
        $new_alias = '/'. $new_content_type .'/'. $split_alias[2];
        $node->path->alias = $new_alias;
        //Save the changes.
        $node->save();
        $node_updated[] = 'Node ID:'. $node->id() .' changed to '. $new_content_type ;
      }
    }
    dsm($node_updated);
    return [
      '#markup' => 'Changed successfully...',
    ];
  }

}