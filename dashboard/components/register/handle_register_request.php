<?php

function switch_selected_tournament_register() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  if (!$tournament) {
    wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
  }
  $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);

  wp_send_json_success(['message' => 'Torneo seleccionado correctamente', 
  'divisions' => create_division_list_selector($divisions),
  'pending_table' => render_pending_teams_from_register($tournament_id, $divisions[0]->division_id), 
  'registered_table' => render_registered_teams_table($divisions[0]->division_id)]);
}

function switch_selected_division_register() {
  if (!isset($_POST['division_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar la division']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $division_id = intval($_POST['division_id']);
  $division = DivisionsDatabase::get_division_by_id($division_id);
  if (!$division) {
    wp_send_json_error(['message' => 'No se pudo encontrar la division seleccionada.']);
  }

  wp_send_json_success(['message' => 'Division seleccionada correctamente', 
  'pending_table' => render_pending_teams_from_register($tournament_id, $division_id), 
  'registered_table' => render_registered_teams_table($division_id)]);
}

function reject_team_register() {
  if (!isset($_POST['record_id'])) {
    wp_send_json_error(['message' => 'No se pudo rechazar el equipo']);
  }
  $record_id = intval($_POST['record_id']);
  $pending_team = TeamRegisterQueueDatabase::get_team_register_queue_by_id($record_id);
  if (!$pending_team) {
    wp_send_json_error(['message' => 'No se pudo encontrar el equipo seleccionado.']);
  }

  TeamRegisterQueueDatabase::delete_team($record_id);
  PendingPlayersDatabase::delete_players_by_team_register_queue($record_id);

  wp_send_json_success(['message' => 'Equipo rechazado correctamente']);
}

function accept_team_register() {
  if (!isset($_POST['record_id'])) {
    wp_send_json_error(['message' => 'No se pudo aceptar el equipo']);
  }
  $record_id = intval($_POST['record_id']);
  $pending_team = TeamRegisterQueueDatabase::get_team_register_queue_by_id($record_id);
  if (!$pending_team) {
    wp_send_json_error(['message' => 'No se pudo encontrar el equipo seleccionado.']);
  }

  $coach_id = $pending_team->coach_id;
  $coach = CoachesUserDatabase::get_coach_by_id($coach_id);
  $team = TeamsUserDatabase::get_team_by_id($pending_team->team_id);

  // Register coach
  $coach_result = CoachesDatabase::insert_coach(
    intval($coach_id),
    $pending_team->tournament_id,
    $coach->coach_name,
    $coach->coach_contact,
    $coach->coach_city,
    $coach->coach_state,
    $coach->coach_country,
  );

  if (!$coach_result[0]) {
    wp_send_json_error(['message' => 'No se pudo registrar el coach.']);
  }

  $division = DivisionsDatabase::get_division_by_id($pending_team->division_id);
  $team_category = $division->division_category;
  $team_mode = $division->division_mode;

  // Register team
  $team_result = TeamsDatabase::insert_team(
    $pending_team->tournament_id, 
    $team->team_name,
    $pending_team->division_id, 
    $team_category, 
    $team_mode, 
    $coach_result[1], 
    $team->team_logo,
    $pending_team->team_id,
  );

  // Register players
  // $players = PendingPlayersDatabase::get_players_by_team_register_queue($record_id);
  // foreach ($players as $player) {
  //   PlayersDatabase::insert_player(
  //     $player->player_name,
  //     $team[1],
  //     $player->player_photo,
  //     $coach[1]
  //   );
  // }
  
  if (!$team_result[0]) {
    wp_send_json_error(['message' => 'No se pudo registrar el equipo.']);
  }

  TeamRegisterQueueDatabase::delete_team($record_id);
  wp_send_json_success(['message' => 'Equipo aceptado correctamente']);
}

add_action('wp_ajax_switch_selected_division_register', 'switch_selected_division_register');
add_action('wp_ajax_nopriv_switch_selected_division_register', 'switch_selected_division_register');
add_action('wp_ajax_switch_selected_tournament_register', 'switch_selected_tournament_register');
add_action('wp_ajax_nopriv_switch_selected_tournament_register', 'switch_selected_tournament_register');
add_action('wp_ajax_reject_team_register', 'reject_team_register');
add_action('wp_ajax_nopriv_reject_team_register', 'reject_team_register');
add_action('wp_ajax_accept_team_register', 'accept_team_register');
add_action('wp_ajax_nopriv_accept_team_register', 'accept_team_register');