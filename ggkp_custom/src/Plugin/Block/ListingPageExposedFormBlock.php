<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "listingPage_exposedForm_block",
 *   admin_label = @Translation("Listing Page Exposed Form Block."),
 * )
 */
class ListingPageExposedFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_url;
    $current_path = \Drupal::service('path.current')->getPath();
    $url = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    if($current_path =='/resources') {
      $view_id = 'research_listing_page';
      $view_display = 'page_1';
    }
    elseif($current_path =='/case-studies') {
      $view_id = 'browse_case_studies';
      $view_display = 'page_1';
    }
    elseif($current_path =='/national-documents') {
      $view_id = 'national_documents';
      $view_display = 'page_1';
    }

    $view = \Drupal\views\Views::getView($view_id);
    $view->setDisplay($view_display);
    $view->initHandlers();
    $form_state = new \Drupal\Core\Form\FormState();
    $form_state->setFormState([
      'view' => $view,
      'display' => $view->display_handler->display,
      'exposed_form_plugin' => $view->display_handler->getPlugin('exposed_form'),
      //'method' => 'get',
      'rerender' => TRUE,
      'no_redirect' => TRUE,
      'always_process' => TRUE, // This is important for handle the form status.
    ]);
    $form_state->setMethod('GET');

    $form = \Drupal::formBuilder()->buildForm('Drupal\views\Form\ViewsExposedForm', $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
