<?php

namespace Drupal\ggkp_custom\Controller;

use Drupal\node\Entity\Node;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\taxonomy\Entity\Term;

/**
 * Taxonomy Cleanup.
  * Utility: clean duplicated taxonomy.
  * @param $termid
  * field organisation content ID
 */
class TaxonomyCleanupController {

  public function index(){
    $path = drupal_get_path('theme', 'ggkp');
    $fileName = $path . '/assets/data/Taxonomy2.xlsx';
    $spreadsheet = IOFactory::load($fileName);
    $sheetData = $spreadsheet->getActiveSheet();
    $highestColumn = $sheetData->getHighestColumn();
    $rows = [];
    // Excel to array.
    foreach ($sheetData->getRowIterator() as $row) {
      $cellIterator = $row->getCellIterator();
      $cellIterator->setIterateOnlyExistingCells(FALSE);
      $cells = [];
      foreach ($cellIterator as $cell) {
        $cells[] = trim($cell->getValue());
      }
      $rows[] = $cells;
    }
    $offset = 1;
    $content_rows = array_slice($rows, $offset);
    $ids = [];
    foreach ($content_rows as $row) {
      if (empty($row[2]) || !is_numeric($row[2])) {
        continue;
      }
      $key = $row[2];
      // Add Translation.
      if (!empty($row[3])) {
        $translate = $this->addTranslation($key,$row[3]);
      }
      $count = count($row);
      // Create IDS Array.
      for ($i=4;$i<$count;$i=$i+2) {
        if (!empty($row[$i]) && is_numeric($row[$i])) {
          $ids[$key][] = $row[$i];
        }
      }
    }
    foreach ($ids as $id_key => $tids) {
      foreach ($tids as $term_id) {
        $nodes = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties([
          'field_organisation' => $term_id,
        ]);
        foreach ($nodes as $node) {
          $current_orgs = $node->get('field_organisation')->getValue();
          // Get the duplicate to remove.
          $index_to_remove = array_search($term_id,array_column($current_orgs,'target_id'));
          $node->get('field_organisation')->removeItem($index_to_remove);
          // Add original if not exist.
          $index_to_add = array_search($id_key,array_column($current_orgs,'target_id'));
          if ($index_to_add === FALSE) {
            $node->get('field_organisation')->appendItem([
              'target_id' => $id_key,
            ]);
          }
          $node->save();
        }
      }
      // Delete duplicates.
      $controller = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
      $duplicate_entities = $controller->loadMultiple($tids);
      $controller->delete($duplicate_entities);
    }
    return [
      '#type' => 'markup',
      '#markup' => "Clean Up completed.",
    ];
  }

  /**
   * Add Translation.
   */
  public function addTranslation($term_id, $translated_value) {
    $term = Term::load($term_id);
    if (!empty($term)) {
      $term->field_organisation_translated = $translated_value;
      $term->save();
    }
    return TRUE;
  }

}
