<?php

function start_tournament() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }

  // verify if tournament is active
  $tournament_id = intval($_POST['tournament_id']);
  // $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  // if ($tournament->tournament_start_date) {
  //   wp_send_json_error(['message' => 'No se pudo iniciar el torneo, torneo ya esta activo']);
  // }

  // make selected tournament active
  // TournamentsDatabase::start_tournament($tournament_id);

  // create divisions brackets
  $divisions = DivisionsDatabase::get_divisions($tournament_id);
  if (!$divisions) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo, no hay divisiones para el torneo']);
  }
  // verify if divisions have teams
  foreach ($divisions as $division) {
    $teams = TeamsDatabase::get_teams_by_division($division->division_id);
    if (!$teams) {
      wp_send_json_error(['message' => 'No se pudo iniciar el torneo, no hay equipos registrados en la division ' . $division->division_name]);
    }
  }

  // create brackets for each division
  foreach ($divisions as $division) {
    BracketsDatabase::insert_bracket($tournament_id, $division->division_id);
  }

  // prepare tournament data for scheduler
  
  $active_tournament = TournamentsDatabase::get_active_tournament();
  if (!$active_tournament) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo, no hay torneo activo']);
  } 

  $tournament_id = $active_tournament->tournament_id;
  $fields5v5_start = $active_tournament->tournament_fields_5v5_start;
  $fields5v5_end = $active_tournament->tournament_fields_5v5_end;
  $fields7v7_start = $active_tournament->tournament_fields_7v7_start;
  $fields7v7_end = $active_tournament->tournament_fields_7v7_end;
  $tournament_days = $active_tournament->tournament_days;
  $divisions = $active_tournament->tournament_divisions;
  $officials = $active_tournament->tournament_officials;

  $tournament_hours = TournamentHoursDatabase::get_tournament_hours($tournament_id);
  
  $hours = [];
  for ($i = 8; $i <= 20; $i++) {
    $hours[] = intval($i);
  }
  
  $days = explode(',', $tournament_days);

  // clean whitespaces from days
  $days = array_map('trim', $days);
  $days_index = array_flip($days);
  
  $scheduleHours = [];
  foreach ($days as $index => $day) {
    $hours = [];
    $t_day = $tournament_hours[$index];

    $hours_start = $t_day->tournament_hours_start;
    $hours_end = $t_day->tournament_hours_end;
    for ($i = $hours_start; $i <= $hours_end; $i++) {
      $hours[] = intval($i);
    }
    $scheduleHours[$index] = $hours;
  }

  $fields5v5 = [];
  for ($i = $fields5v5_start; $i <= $fields5v5_end; $i++) {
    $fields5v5[] = intval($i);
  }
  $fields7v7 = [];
  for ($i = $fields7v7_start; $i <= $fields7v7_end; $i++) {
    $fields7v7[] = intval($i);
  }
  
  $divisions_data = DivisionsDatabase::get_divisions($tournament_id);
  $officials_data = OfficialsDatabase::get_officials();
  
  $divisions = [];
  foreach ($divisions_data as $division) {
    $bracket_id = BracketsDatabase::get_bracket_by_division($division->division_id, $tournament_id)->bracket_id;
    $teams_data = TeamsDatabase::get_teams_by_division($division->division_id);
    $teams = [];
    foreach ($teams_data as $team) {
      $teams[] = $team->team_id;
    }
    $divisions[] = [ "id"=> $division->division_id, "teams"=> $teams, "division_mode"=> $division->division_mode, "bracket_id"=> $bracket_id];
  }
  
  $officials = [];
  foreach ($officials_data as $official) {
    // clean whitespaces from days
    $calendar_days = explode(',', $official->official_schedule);
    $calendar_days = array_map('trim', $calendar_days);

    $official->days = [];
    foreach ($calendar_days as $day) {
      $day_index = $days_index[$day];
      $official->days[] = $day_index;
    }
    $official->hours = $official->official_hours;
    $official->mode = $official->official_mode;
    $official->tournament_id = $tournament_id;
    $officials[] = ["id" => $official->official_id, "days" => $official->days, "hours" => $official->hours, "mode" => $official->mode];
  }
  // create matches for each bracket
  $Tournament_Scheduler = new TournamentScheduler($scheduleHours, $fields5v5, $fields7v7, $divisions, $officials, intval($tournament_id), $days);
  $result = $Tournament_Scheduler->scheduleMatches();
  wp_send_json_success(['message' => 'Torneo iniciado correctamente', 'result' => $result]);
}

function on_add_tournament($tournament) {
  $html = "";
  $html .= "<div class='table-row'>
              <span class='table-cell-header'>Torneo:</span>
              <span class='table-cell'>" . esc_html($tournament->tournament_name) . "</span>
            </div>
            <div class='table-row'>
              <span class='table-cell-header'>Calendario:</span>
              <span class='table-cell'>
              <input type='text' id='tournament-selected-days' readonly value='$tournament->tournament_days'>
              </span>
            </div>
            <div class='table-row'>
              <span class='table-cell-header'>Horarios:</span>
              <div id='tournament-hours' class='table-cell-column'>
              " . create_tournament_hours($tournament->tournament_id) . "
              </div>
            </div>
            <div class='table-row'>
              <span class='table-cell-header'>Campos 5v5:</span>
              <span class='table-cell'>" . esc_html($tournament->tournament_fields_5v5_start) . " - " . esc_html($tournament->tournament_fields_5v5_end) . "</span>
            </div>
            <div class='table-row'>
              <span class='table-cell-header'>Campos 7v7:</span>
              <span class='table-cell'>" . esc_html($tournament->tournament_fields_7v7_start) . " - " . esc_html($tournament->tournament_fields_7v7_end) . "</span>
            </div>
            <div class='table-row'>
              <span class='table-cell-header'>Acciones:</span>
              <div class='table-cell-column'>
                <button id='start-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Iniciar Torneo</button>
                <button id='delete-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Eliminar</button>
              </div>
            </div>";
  return $html;
}

function delete_tournament() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  TournamentsDatabase::delete_tournament($tournament_id);
  wp_send_json_success(['message' => 'Torneo eliminado correctamente']); 
}

function add_tournament() {
  if (!isset($_POST['tournament_name']) || !isset($_POST['tournament_days']) || !isset($_POST['tournament_hours']) || !isset($_POST['tournament_fields_5v5_start']) || !isset($_POST['tournament_fields_5v5_end']) || !isset($_POST['tournament_fields_7v7_start']) || !isset($_POST['tournament_fields_7v7_end'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $tournament = TournamentsDatabase::get_active_tournament();
  if ($tournament) {
    wp_send_json_error(['message' => 'Ya hay un torneo activo.']);
  }

  $tournament_name = sanitize_text_field($_POST['tournament_name']);
  $tournament_days = sanitize_text_field($_POST['tournament_days']);
  $tournament_hours = $_POST['tournament_hours'];
  $tournament_fields_5v5_start = intval($_POST['tournament_fields_5v5_start']);
  $tournament_fields_5v5_end = intval($_POST['tournament_fields_5v5_end']);
  $tournament_fields_7v7_start = intval($_POST['tournament_fields_7v7_start']);
  $tournament_fields_7v7_end = intval($_POST['tournament_fields_7v7_end']);
  $tournament_creation_date = date('Y-m-d');

  $result = TournamentsDatabase::insert_tournament($tournament_name, $tournament_days, $tournament_fields_5v5_start, $tournament_fields_5v5_end, $tournament_fields_7v7_start, $tournament_fields_7v7_end, $tournament_creation_date );
  if ($result) {
    $days = explode(',', $tournament_days);
    $days = array_map('trim', $days);

    foreach ($days as $index => $day) {
      $hours = $tournament_hours[$index];
      TournamentHoursDatabase::insert_tournament_hours($tournament->tournament_id, $day, intval($hours[0]), intval($hours[1]));
    }
    
    wp_send_json_success(['message' => 'Torneo agregado correctamente', 'html' => on_add_tournament($tournament)]);
  }
  wp_send_json_error(['message' => 'Torneo no agregado, torneo ya existe']);
}

function get_tournament_days() {
  $active_tournament = TournamentsDatabase::get_active_tournament();
  wp_send_json_success(['days' => $active_tournament->tournament_days]);
}

add_action('wp_ajax_delete_tournament', 'delete_tournament');
add_action('wp_ajax_nopriv_delete_tournament', 'delete_tournament');
add_action('wp_ajax_add_tournament', 'add_tournament');
add_action('wp_ajax_nopriv_add_tournament', 'add_tournament');
add_action('wp_ajax_start_tournament', 'start_tournament');
add_action('wp_ajax_nopriv_start_tournament', 'start_tournament');
add_action('wp_ajax_get_tournament_days', 'get_tournament_days');
add_action('wp_ajax_nopriv_get_tournament_days', 'get_tournament_days');
