<?php

if (!function_exists('switch_selected_tournament_matches_schedule')) {
  function switch_selected_tournament_matches_schedule() {
    if (!isset($_POST['tournament_id'])) {
      wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
    }
    $tournament_id = intval($_POST['tournament_id']);
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    if (!$tournament) {
      wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
    }
    wp_send_json_success([
      'message' => 'Torneo seleccionado correctamente', 
      'schedule' => create_table_for_matches_schedule($tournament)
      ]);
  }
}
add_action('wp_ajax_switch_selected_tournament_matches_schedule', 'switch_selected_tournament_matches_schedule');
add_action('wp_ajax_nopriv_switch_selected_tournament_matches_schedule', 'switch_selected_tournament_matches_schedule');