<?php

function generate_match_links($brackets) {
  //$links = [];
  foreach ($brackets as $key => $bracket) {
    if (!isset($bracket['scheduled_matches'])) {
      continue;
    }
    $total_matches = array_sum($bracket['matches_per_round']);
    for ($round = count($bracket['scheduled_matches']) -1; $round > 0; $round--) {
      $matches_this_round = count($bracket['scheduled_matches'][$round]);
      $counter = $total_matches - $matches_this_round;
      
      $matches = array_reverse($bracket['scheduled_matches'][$round]);
      for ($index = 0; $index < $matches_this_round; $index++) {
        if ($matches[$index]["team_id_1"] == "TBD" && $matches[$index]["team_id_2"] == "TBD") {
          $match = PendingMatchesDatabase::get_match_by_bracket_match($total_matches, intval($bracket["bracket_id"]));

          $match_link_2 = $counter--;
          $match_link_1 = $counter;

          PendingMatchesDatabase::update_match_link($match->match_id, $match_link_1, $match_link_2);
        	$counter --;
      	} elseif ($matches[$index]["team_id_1"] == "TBD" || $matches[$index]["team_id_2"] == "TBD") {
          $match = PendingMatchesDatabase::get_match_by_bracket_match($total_matches, intval($bracket["bracket_id"]));
          if ($matches[$index]["team_id_1"] == "TBD") {
            PendingMatchesDatabase::update_match_link($match->match_id, $counter, null);
          } else {
            PendingMatchesDatabase::update_match_link($match->match_id, null, $counter);
          }
        	$counter --;
      	}
        
        $total_matches --;
      }
    }
    
  }

  //return $links;
}

function create_brackets() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }

  // verify if tournament is active
  $tournament_id = intval($_POST['tournament_id']);
 
  data_checks($tournament_id);

  // start tournament with single elimination type
  TournamentsDatabase::start_tournament($tournament_id, 1);

  // create divisions brackets
  $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);

  // create brackets for each division
  foreach ($divisions as $division) {
    $teams = TeamsDatabase::get_enrolled_teams_by_division($division->division_id);
    if (count($teams) >= $division->division_min_teams) {
      BracketsDatabase::insert_bracket($tournament_id, $division->division_id);
    }
  }

  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  // prepare tournament data for scheduler
  $tournament_id = $tournament->tournament_id;
  $fields5v5_start = $tournament->tournament_fields_5v5_start;
  $fields5v5_end = $tournament->tournament_fields_5v5_end;
  $fields7v7_start = $tournament->tournament_fields_7v7_start;
  $fields7v7_end = $tournament->tournament_fields_7v7_end;
  $tournament_days = $tournament->tournament_days;
  $officials = $tournament->tournament_officials;

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
  
  $divisions_data = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  $officials_data = OfficialsDatabase::get_officials();
  
  $divisions = [];
  foreach ($divisions_data as $division) {
    $bracket_id = BracketsDatabase::get_bracket_by_division($division->division_id, $tournament_id)->bracket_id;
    $teams_data = TeamsDatabase::get_enrolled_teams_by_division($division->division_id);
    $teams = [];
    foreach ($teams_data as $team) {
      if ($team->is_enrolled) {
        $teams[] = $team->team_id;
      }
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
  $Tournament_Scheduler = new TournamentScheduler($scheduleHours, $fields5v5, $fields7v7, $officials, intval($tournament_id), $days);
  $Tournament_Scheduler->createMatchesForBrackets($divisions);
  $brackets = $Tournament_Scheduler->getBrackets();
  $links = generate_match_links($brackets);

  $brackets_dropdown = generate_brackets_dropdown($tournament_id);
  wp_send_json_success(['message' => 'Brackets creados correctamente', 'brackets_dropdown' => $brackets_dropdown, 'links' => $links, 'brackets' => $brackets]);
}

function create_round_robin() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);

  data_checks($tournament_id);
  
  TournamentsDatabase::start_tournament($tournament_id, 2);
  
  $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  // create brackets for each division
  foreach ($divisions as $division) {
    $teams = TeamsDatabase::get_teams_by_division($division->division_id);
    if (count($teams) >= $division->division_min_teams) {
      BracketsDatabase::insert_bracket($tournament_id, $division->division_id);
    }
  }
  
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  // prepare tournament data for scheduler
  $tournament_id = $tournament->tournament_id;
  $fields5v5_start = $tournament->tournament_fields_5v5_start;
  $fields5v5_end = $tournament->tournament_fields_5v5_end;
  $fields7v7_start = $tournament->tournament_fields_7v7_start;
  $fields7v7_end = $tournament->tournament_fields_7v7_end;
  $officials = $tournament->tournament_officials;

  $hours_schedule = create_hours_schedule($tournament_id, $tournament->tournament_days);
  $scheduleHours = $hours_schedule['scheduleHours'];
  $days = $hours_schedule['days'];

  $fields5v5 = [];
  for ($i = $fields5v5_start; $i <= $fields5v5_end; $i++) {
    $fields5v5[] = intval($i);
  }
  $fields7v7 = [];
  for ($i = $fields7v7_start; $i <= $fields7v7_end; $i++) {
    $fields7v7[] = intval($i);
  }
  
  $divisions_data = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  
  $divisions = [];
  foreach ($divisions_data as $division) {
    $bracket_id = BracketsDatabase::get_bracket_by_division($division->division_id, $tournament_id)->bracket_id;
    $teams_data = TeamsDatabase::get_teams_by_division($division->division_id);
    $teams = [];
    foreach ($teams_data as $team) {
      if ($team->is_enrolled) {
        $teams[] = $team->team_id;
      }
    }
    $divisions[] = [ "id"=> $division->division_id, "teams"=> $teams, "division_mode"=> $division->division_mode, "bracket_id"=> $bracket_id];
  }

  // create matches for each bracket
  $Tournament_Scheduler = new TournamentScheduler($scheduleHours, $fields5v5, $fields7v7, $officials, intval($tournament_id), $days);
  $Tournament_Scheduler->createMatchesForRoundRobin($divisions);
  $brackets = $Tournament_Scheduler->getBrackets();

  wp_send_json_success(['message' => 'Round Robin creado correctamente', 'brackets' => $brackets]);
}

function create_hours_schedule($tournament_id, $tournament_days){
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

  return ['scheduleHours' => $scheduleHours, 'days' => $days];
}

function data_checks($tournament_id) {
  // verify if tournament is active
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  if ($tournament->tournament_start_date) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo, torneo ya esta activo']);
  }

  // create divisions brackets
  $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  if (!$divisions) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo, no hay divisiones activas']);
  }

  // verify if divisions have teams
  foreach ($divisions as $division) {
    $teams = TeamsDatabase::get_teams_by_division($division->division_id);
    if (!$teams) {
      wp_send_json_error(['message' => 'No se pudo iniciar el torneo, no hay equipos registrados en la division ' . $division->division_name]);
    }
  }
}

function delete_brackets() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $matches = MatchesDatabase::delete_matches_by_tournament($tournament_id);
  $pending_matches = PendingMatchesDatabase::delete_pending_matches_by_tournament($tournament_id);
  $result = BracketsDatabase::delete_brackets_by_tournament($tournament_id);
  TournamentsDatabase::reset_tournament($tournament_id);

  if ($result && $pending_matches && $matches) {
    wp_send_json_success(['message' => 'Brackets eliminados correctamente']); 
  }
  wp_send_json_error(['message' => 'No se pudo eliminar el torneo']);
}

function on_add_tournament($tournament) {
  $html = "";
  $html = "<div class='tournament-data' id='tournament-" . esc_attr($tournament->tournament_id) . "'>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Torneo:</span>
              <span class='tournament-table-cell'>" . esc_html($tournament->tournament_name) . "</span>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Calendario:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='tournament-selected-days' readonly value='$tournament->tournament_days'>
              </div>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Horarios:</span>
              <div id='tournament-hours' class='tournament-table-cell-column'>
              " . create_tournament_hours($tournament->tournament_id) . "
              </div>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 5v5:</span>
              <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_5v5_start) . " - " . esc_html($tournament->tournament_fields_5v5_end) . "</span>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 7v7:</span>
              <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_7v7_start) . " - " . esc_html($tournament->tournament_fields_7v7_end) . "</span>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Acciones:</span>
              <div class='tournament-table-cell-column'>
                <button class='base-button pending-button' id='create-brackets-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Crear Brackets</button>
                <button class='base-button pending-button' id='assign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' disabled>Asignar Arbitros</button>
                <button class='base-button danger-button' id='delete-brackets-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' disabled>Eliminar Brackets</button>
                <button class='base-button danger-button' id='finish-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Finalizar Torneo</button>
                <button class='base-button danger-button' id='delete-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Eliminar Torneo</button>
              </div>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Resultado:</span>
              <span class='tournament-table-cell' id='tournament-result-table'>Resultado de la accion.</span>
            </div>
          </div>
        ";
  return $html;
}

function on_add_tournament_entry($tournament) {
  $html = "";
  $html .= "<div class='tournament-item' id='tournament-" . esc_attr($tournament->tournament_id) . "'>";
  $html .= "<span class='tournament-item-name' >" . esc_html($tournament->tournament_name) . "</span>";
  $html .= "</div>";
  return $html;
}

function delete_tournament() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $result = TournamentsDatabase::delete_tournament($tournament_id);
  if ($result) {
    PendingMatchesDatabase::delete_pending_matches_by_tournament($tournament_id);
    MatchesDatabase::delete_matches_by_tournament($tournament_id);
    BracketsDatabase::delete_brackets_by_tournament($tournament_id);
    TournamentHoursDatabase::delete_tournament_hours_by_tournament($tournament_id);

    wp_send_json_success(['message' => 'Torneo eliminado correctamente']); 
  }
  wp_send_json_error(['message' => 'No se pudo eliminar el torneo']);
}

function add_tournament() {
  if (!isset($_POST['tournament_name']) || !isset($_POST['tournament_days']) || !isset($_POST['tournament_hours']) || !isset($_POST['tournament_fields_5v5_start']) || !isset($_POST['tournament_fields_5v5_end']) || !isset($_POST['tournament_fields_7v7_start']) || !isset($_POST['tournament_fields_7v7_end'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
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
  if ($result['success']) {
    $days = explode(',', $tournament_days);
    $days = array_map('trim', $days);
    $tournament = TournamentsDatabase::get_tournament_by_id($result['tournament_id']);
    foreach ($days as $index => $day) {
      $hours = $tournament_hours[$index];
      TournamentHoursDatabase::insert_tournament_hours($tournament->tournament_id, $day, intval($hours[0]), intval($hours[1]));
    }
    
    wp_send_json_success(['message' => 'Torneo agregado correctamente', 'html' => on_add_tournament($tournament), 'tournament_entry' => on_add_tournament_entry($tournament)]);
  }
  wp_send_json_error(['message' => 'Torneo no agregado, torneo ya existe']);
}

function switch_selected_tournament() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  $tournament_hours = TournamentHoursDatabase::get_tournament_hours_by_tournament($tournament_id);
  if (!$tournament) {
    wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
  }
  wp_send_json_success([
    'message' => 'Torneo seleccionado correctamente', 
    'divisions' => cuicpro_divisions($tournament_id), 
    'coaches' => cuicpro_coaches_table($tournament_id), 
    'brackets' => generate_brackets_dropdown($tournament_id),
    'officials' => render_officials($tournament_id),
    'teams' => cuicpro_teams_by_coach($tournament_id) . cuicpro_teams_by_division($tournament_id),
    'tournament_days' => $tournament->tournament_days,
    'official_hours' => create_hours_select_input($tournament_hours),
    'matches' => cuicpro_matches($tournament_id)
    ]);
}

function get_tournament_days() {
  $active_tournament = TournamentsDatabase::get_active_tournament();
  wp_send_json_success(['days' => $active_tournament->tournament_days]);
}

add_action('wp_ajax_create_round_robin', 'create_round_robin');
add_action('wp_ajax_nopriv_create_round_robin', 'create_round_robin');
add_action('wp_ajax_switch_selected_tournament', 'switch_selected_tournament');
add_action('wp_ajax_nopriv_switch_selected_tournament', 'switch_selected_tournament');
add_action('wp_ajax_create_brackets', 'create_brackets');
add_action('wp_ajax_nopriv_create_brackets', 'create_brackets');
add_action('wp_ajax_delete_brackets', 'delete_brackets');
add_action('wp_ajax_nopriv_delete_brackets', 'delete_brackets');
add_action('wp_ajax_assign_officials', 'assign_officials');
add_action('wp_ajax_nopriv_assign_officials', 'assign_officials');
add_action('wp_ajax_delete_tournament', 'delete_tournament');
add_action('wp_ajax_nopriv_delete_tournament', 'delete_tournament');
add_action('wp_ajax_add_tournament', 'add_tournament');
add_action('wp_ajax_nopriv_add_tournament', 'add_tournament');
add_action('wp_ajax_get_tournament_days', 'get_tournament_days');
add_action('wp_ajax_nopriv_get_tournament_days', 'get_tournament_days');
