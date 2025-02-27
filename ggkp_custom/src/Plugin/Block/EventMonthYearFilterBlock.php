<?php

namespace Drupal\ggkp_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "event_month_year_filter_block",
 *   admin_label = @Translation("Event Month Year Filter Block."),
 * )
 */
class EventMonthYearFilterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => block_show_content(),
      '#allowed_tags' => ['h3', 'select', 'div', 'input', 'p', 'span', 'label', 'table', 'tr', 'th', 'td', 'thead', 'tbody',
        'a', 'section', 'ul', 'li', 'img', 'option',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}

/**
 * Custom function reset button.
 */
function block_show_content() {
  global $base_url;
  $current_path = \Drupal::service('path.current')->getPath();
  $url = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
  $date = \Drupal::request()->query->get('date');
  $selected_month = substr($date, 0, 4);
  $selected_year = substr($date, 4);
  
  $output = '<div class ="calendar-month-year-event">';
  if(isset($date)) {
    $output .= '<input type="hidden" value="' . $selected_month . '" name="selected-month">
            <input type="hidden" value="' . $selected_year . '" name="selected-year">';
  }

  $output .= '<select id="month" name="month">
      <option id="00" value="None">Select Month</option>
      <option id="01" value="January">January</option>
      <option id="02" value="February">February</option>
      <option id="03" value="March">March</option>
      <option id="04" value="April">April</option>
      <option id="05" value="May">May</option>
      <option id="06" value="June">June</option>
      <option id="07" value="July">July</option>
      <option id="08" value="August">August</option>
      <option id="09" value="September">September</option>
      <option id="10" value="October">October</option>
      <option id="11" value="November">November</option>
      <option id="12" value="December">December</option>
    </select>';
    
  $currentYear = date("Y");
  $year = '';
	for ($i = ( $currentYear - 3); $i <= ( $currentYear + 3); $i++) {
	  $year .= '<option id="' . $i . '" value="' . $i . '">' . $i . '</option>';
	}
  $output .= '<select id="year" name="year">
              <option id="00" value="None">Select Year</option>
              ' . $year . '</select>';
   
  $output .= '<a id="gotomonth">Submit</a>';
  
  $output .= '</div>';

  return $output;
}
