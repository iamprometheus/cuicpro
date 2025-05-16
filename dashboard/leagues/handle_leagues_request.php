<?php
function delete_league() {
  if (!isset($_POST['league_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar la liga']);
  }
  if (TeamsDatabase::get_teams_by_league(intval($_POST['league_id']))) {
    wp_send_json_error(['message' => 'No se pudo eliminar la liga, hay equipos asociados']);
  }
  $league_id = intval($_POST['league_id']);
  LeaguesDatabase::delete_league($league_id);
  wp_send_json_success(['message' => 'Liga eliminada correctamente']);
}

function add_league() {
  if (!isset($_POST['league_name']) || !isset($_POST['league_mode'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $league_name = sanitize_text_field($_POST['league_name']);
  $league_mode = sanitize_text_field($_POST['league_mode']);
  $result = LeaguesDatabase::insert_league($league_name, $league_mode);
  if ($result) {
    $league = LeaguesDatabase::get_league_by_name($league_name);
    wp_send_json_success(['message' => 'Liga agregada correctamente', 'league' => $league]);
  }
  wp_send_json_error(['message' => 'Liga no agregada, liga ya existe']);
}

add_action('wp_ajax_delete_league', 'delete_league');
add_action('wp_ajax_nopriv_delete_league', 'delete_league');
add_action('wp_ajax_add_league', 'add_league');
add_action('wp_ajax_nopriv_add_league', 'add_league');
