<?php

function fetch_division_playoffs() {
  if (!isset($_POST['division_id']) || !isset($_POST['tournament_id'])) {
    wp_send_json_error(array('message' => 'Division ID is required!'));
  }

  $division_id = $_POST['division_id'];
  $tournament_id = $_POST['tournament_id'];
  $bracket_id = BracketsDatabase::get_bracket_by_division($division_id, $tournament_id)->bracket_id;
  $result = render_playoffs_results($bracket_id);
  $html = $result['html'];
  $elements = $result['elements'];
  $matches = $result['matches'];

  wp_send_json_success(array('message' => 'Teams fetched successfully!', 'html' => $html, 'elements' => $elements, 'matches' => $matches));
}

function fetch_tournament_divisions_playoffs() {
    if (!isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Tournament ID is required!'));
    }
    
    $tournament_id = $_POST['tournament_id'];
    $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
    $divisions_html = "";

    if (empty($divisions)) {
      $divisions_html .= "<option value=''>No hay divisiones registradas en este torneo</option>";
      wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'divisions' => $divisions_html, 'html' => "", 'elements' => null, 'matches' => null));
    }

    foreach ($divisions as $division) {
        $divisions_html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . "</option>";
    }

    $bracket_id = BracketsDatabase::get_bracket_by_division($divisions[0]->division_id, $tournament_id)->bracket_id;
    $result = render_playoffs_results($bracket_id);
    $html = $result['html'];
    $elements = $result['elements'];
    $matches = $result['matches'];
    

    wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'divisions' => $divisions_html, 'html' => $html, 'elements' => $elements, 'matches' => $matches));
}

add_action('wp_ajax_fetch_division_playoffs', 'fetch_division_playoffs');
add_action('wp_ajax_nopriv_fetch_division_playoffs', 'fetch_division_playoffs');
add_action('wp_ajax_fetch_tournament_divisions_playoffs', 'fetch_tournament_divisions_playoffs');
add_action('wp_ajax_nopriv_fetch_tournament_divisions_playoffs', 'fetch_tournament_divisions_playoffs');
