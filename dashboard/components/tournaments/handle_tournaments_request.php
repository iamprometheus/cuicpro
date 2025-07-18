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

function generate_match_link_multibracket($brackets) {
  foreach ($brackets as $bracket) {
    $playoffs_matches = PendingMatchesDatabase::get_matches_by_type(2, intval($bracket["bracket_id"]));
    $playoff_id = 1;
    foreach ($bracket['matches_single_elimination'] as $playoff) {

      $playoff_matches = array_filter($playoffs_matches, function($match) use ($playoff_id) {
        return $match->playoff_id == $playoff_id;
      });

      $playoff_matches = array_values($playoff_matches);

      if (count($playoff_matches) == 0) {
        $playoff_id++;
        continue;
      }

      $rounds = array_unique(array_map(function($match) {
        return $match->bracket_round;
      }, $playoff_matches));

      $total_matches = count($playoff_matches) - 1;
      $counter = $playoff_matches[$total_matches]->bracket_match - 1;
      for ($round = count($rounds) - 1; $round > 0; $round--) {
        $matches = array_filter($playoff_matches, function($match) use ($round) {
          return $match->bracket_round == $round;
        });
        $matches_this_round = count($matches);
        $matches = array_reverse($matches);

        for ($index = 0; $index < $matches_this_round; $index++) {
          if ($matches[$index]->team_id_1 == null && $matches[$index]->team_id_2 == null) {
            $match_link_2 = $counter--;
            $match_link_1 = $counter;
            $match_id = $matches[$index]->match_id;

            PendingMatchesDatabase::update_match_link($match_id, $match_link_1, $match_link_2);
            $counter--;
          } elseif ($matches[$index]->team_id_1 == null || $matches[$index]->team_id_2 == null) {
            if ($matches[$index]->team_id_1 == null) {
              PendingMatchesDatabase::update_match_link($matches[$index]->match_id, $counter, null);
            } else {
              PendingMatchesDatabase::update_match_link($matches[$index]->match_id, null, $counter);
            }
            $counter--;
          }
          
          $total_matches--;
        }
      }
      $playoff_id++;
    }
  }
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
  $tournament_fields5v5 = $tournament->tournament_fields_5v5;
  $tournament_fields7v7 = $tournament->tournament_fields_7v7;
  $tournament_days = $tournament->tournament_days;

  $tournament_hours = TournamentHoursDatabase::get_tournament_hours($tournament_id);
  
  $hours = [];
  for ($i = 8; $i <= 20; $i++) {
    $hours[] = intval($i);
  }
  
  $days = explode(',', $tournament_days);

  // clean whitespaces from days
  $days = array_map('trim', $days);
  
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
  for ($i = 1; $i <= $tournament_fields5v5; $i++) {
    $fields5v5[] = intval($i);
  }
  $fields7v7 = [];
  for ($i = $tournament_fields5v5 + 1; $i <= $tournament_fields7v7 + $tournament_fields5v5; $i++) {
    $fields7v7[] = intval($i);
  }
  
  $divisions_data = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  
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
  
  // create matches for each bracket
  $Tournament_Scheduler = new TournamentScheduler($scheduleHours, $fields5v5, $fields7v7, intval($tournament_id), $days);
  $Tournament_Scheduler->createMatchesForBrackets($divisions);
  $brackets = $Tournament_Scheduler->getBrackets();
  generate_match_links($brackets);
  
  $brackets_dropdown = generate_brackets_dropdown($tournament);
  wp_send_json_success(['message' => 'Brackets creados correctamente', 'brackets_dropdown' => $brackets_dropdown, 'brackets' => $brackets]);
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
  $tournament_fields5v5 = $tournament->tournament_fields_5v5;
  $tournament_fields7v7 = $tournament->tournament_fields_7v7;

  $hours_schedule = create_hours_schedule($tournament_id, $tournament->tournament_days);
  $scheduleHours = $hours_schedule['scheduleHours'];
  $days = $hours_schedule['days'];

  $fields5v5 = [];
  for ($i = 1; $i <= $tournament_fields5v5; $i++) {
    $fields5v5[] = intval($i);
  }
  $fields7v7 = [];
  for ($i = $tournament_fields5v5 + 1; $i <= $tournament_fields7v7 + $tournament_fields5v5; $i++) {
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
  $Tournament_Scheduler = new TournamentScheduler($scheduleHours, $fields5v5, $fields7v7, intval($tournament_id), $days);
  $Tournament_Scheduler->createMatchesForRoundRobin($divisions);
  $brackets = $Tournament_Scheduler->getBrackets();

  $brackets_dropdown = generate_brackets_dropdown($tournament);
  wp_send_json_success(['message' => 'Round Robin creado correctamente', 'brackets' => $brackets, 'brackets_dropdown' => $brackets_dropdown]);
}

function create_general_tournament() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);

  data_checks($tournament_id);
  
  TournamentsDatabase::start_tournament($tournament_id, 3);
  
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
  $tournament_fields5v5 = $tournament->tournament_fields_5v5;
  $tournament_fields7v7 = $tournament->tournament_fields_7v7;

  $hours_schedule = create_hours_schedule($tournament_id, $tournament->tournament_days);
  $scheduleHours = $hours_schedule['scheduleHours'];
  $days = $hours_schedule['days'];

  $fields5v5 = [];
  for ($i = 1; $i <= $tournament_fields5v5; $i++) {
    $fields5v5[] = intval($i);
  }
  $fields7v7 = [];
  for ($i = $tournament_fields5v5 + 1; $i <= $tournament_fields7v7 + $tournament_fields5v5; $i++) {
    $fields7v7[] = intval($i);
  }
  
  $divisions_data = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  
  $divisions = [];
  foreach ($divisions_data as $division) {
    $bracket_id = BracketsDatabase::get_bracket_by_division($division->division_id, $tournament_id)->bracket_id;
    $teams_data = TeamsDatabase::get_teams_by_division($division->division_id);
    $preferred_days = explode(',', $division->division_preferred_days);
    $teams = [];
    foreach ($teams_data as $team) {
      if ($team->is_enrolled) {
        $teams[] = $team->team_id;
      }
    }
    shuffle($teams);
    $divisions[] = [ 
      "id"=> $division->division_id, 
      "teams"=> $teams, 
      "division_mode"=> $division->division_mode, 
      "bracket_id"=> $bracket_id, 
      "preferred_days"=> $preferred_days
    ];
  }

  // create matches for each bracket
  $Tournament_Scheduler = new TournamentScheduler($scheduleHours, $fields5v5, $fields7v7, intval($tournament_id), $days);
  $brackets = $Tournament_Scheduler->createMatchesForGeneralTournament($divisions);
  $brackets_playoffs = $Tournament_Scheduler->createMatchesForPlayoffs($brackets);
  $result = generate_match_link_multibracket($brackets);

  $brackets_dropdown = generate_brackets_dropdown($tournament);
  wp_send_json_success(['message' => 'Partidos creados correctamente', 'brackets' => $brackets, 'brackets_dropdown' => $brackets_dropdown, 'brackets_playoffs' => $brackets_playoffs, 'result' => $result]);
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

  // verify if divisions have minimum enorlled teams
  $has_minimum_teams = false;
  foreach ($divisions as $division) {
    $teams = TeamsDatabase::get_enrolled_teams_by_division($division->division_id);
    if (count($teams) >= $division->division_min_teams) {
      $has_minimum_teams = true;
    }
  }

  if (!$has_minimum_teams) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo, no hay equipos minimos en alguna division']);
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
  $has_officials = unset_officials_from_matches($tournament_id);

  TournamentsDatabase::reset_tournament($tournament_id);

  if ($result && $pending_matches && $matches && $has_officials) {
    wp_send_json_success(['message' => 'Brackets eliminados correctamente']); 
  }
  wp_send_json_error(['message' => 'No se pudo eliminar el torneo']);
}

function on_add_tournament($tournament) {
  $brackets = BracketsDatabase::get_brackets_by_tournament($tournament->tournament_id);
  $pending_matches = PendingMatchesDatabase::get_pending_matches_by_tournament($tournament->tournament_id);
  $has_matches = $brackets ? true : false;
  $has_officials = $tournament->tournament_has_officials == 1 ? true : false;
  $has_pending_matches = $pending_matches ? true : false;

  $assign_officials_disabled = '';
  $unassign_officials_disabled = '';
  $select_bracket_type_disabled = '';
  $delete_matches_disabled = '';
  $finish_tournament_disabled = '';

  if ($has_matches) {
    $select_bracket_type_disabled = 'disabled';
    if ($has_pending_matches) {
      $finish_tournament_disabled = 'disabled';
    }
    if (!$has_officials) {
      $unassign_officials_disabled = 'disabled';
    }
    if ($has_officials) {
      $assign_officials_disabled = 'disabled';
    }
  }

  if (!$has_matches) {
    $finish_tournament_disabled = 'disabled';
    $assign_officials_disabled = 'disabled';
    $unassign_officials_disabled = 'disabled';
    $delete_matches_disabled = 'disabled';
  }

  $tournament_days = str_replace(',', ', ', $tournament->tournament_days);

  $html = "";
  $html = "<div class='tournament-data' id='tournament-" . esc_attr($tournament->tournament_id) . "'>
    <div class='tournament-table-row'>
      <span class='tournament-table-cell-header'>Torneo:</span>
      <span class='tournament-table-cell'>" . esc_html($tournament->tournament_name) . "</span>
    </div>
    <div class='tournament-table-row'>
      <span class='tournament-table-cell-header'>Calendario:</span>
      <div class='tournament-table-cell'>
        <input type='text' id='tournament-selected-days' readonly value='$tournament_days'>
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
      <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_5v5) . "</span>
    </div>
    <div class='tournament-table-row'>
      <span class='tournament-table-cell-header'>Campos 7v7:</span>
      <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_7v7) . "</span>
    </div>
    <div class='tournament-table-row'>
      <span class='tournament-table-cell-header'>Acciones:</span>
      <div class='tournament-table-cell-column'>
        <button class='base-button pending-button' id='edit-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $select_bracket_type_disabled>Editar torneo</button>
        <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
        <span style='text-align: center;'>Tipo de torneo:</span>
        <button class='base-button pending-button' id='create-general-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $select_bracket_type_disabled>Generar Partidos (Liguilla + Playoffs)</button>
        <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
        <button class='base-button pending-button' id='assign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $assign_officials_disabled>Asignar Arbitros</button>
        <button class='base-button danger-button' id='unassign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $unassign_officials_disabled>Desasignar Arbitros</button>
        <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
        <button class='base-button danger-button' id='delete-matches-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $delete_matches_disabled>Eliminar Partidos</button>
        <button class='base-button danger-button' id='finish-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $finish_tournament_disabled>Finalizar Torneo</button>
        <button class='base-button danger-button' id='delete-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' >Eliminar Torneo</button>
      </div>
    </div>
    <div class='tournament-table-row' id='tournament-result-table-container'>
      <span class='tournament-table-cell-header'>Resultado:</span>
      <span class='tournament-table-cell' id='tournament-result-table-" . esc_attr($tournament->tournament_id) . "'>Resultado de la accion.</span>
    </div>
  </div>";
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
  if (!isset($_POST['tournament_name']) || !isset($_POST['tournament_days']) || !isset($_POST['tournament_hours']) || !isset($_POST['tournament_fields_5v5']) || !isset($_POST['tournament_fields_7v7'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $tournament_name = sanitize_text_field($_POST['tournament_name']);
  $tournament_days = sanitize_text_field($_POST['tournament_days']);
  $tournament_hours = $_POST['tournament_hours'];
  $tournament_fields_5v5 = intval($_POST['tournament_fields_5v5']);
  $tournament_fields_7v7 = intval($_POST['tournament_fields_7v7']);
  $tournament_creation_date = date('Y-m-d');

  $result = TournamentsDatabase::insert_tournament($tournament_name, $tournament_days, $tournament_fields_5v5, $tournament_fields_7v7, $tournament_creation_date );
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
  wp_send_json_error(['message' => 'Torneo no agregado, torneo con ese nombre ya existe']);
}

function edit_tournament() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $tournament_id = intval($_POST['tournament_id']);
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  $tournament_hours = TournamentHoursDatabase::get_tournament_hours_by_tournament($tournament_id);
  if (!$tournament) {
    wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
  }
  wp_send_json_success(['message' => 'Torneo seleccionado correctamente', 'tournament' => $tournament, 'tournament_hours' => $tournament_hours]);
}

function update_tournament() {
  if (!isset($_POST['tournament_id']) || !isset($_POST['tournament_name']) || !isset($_POST['tournament_days']) || !isset($_POST['tournament_hours']) || !isset($_POST['tournament_fields_5v5']) || !isset($_POST['tournament_fields_7v7'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $tournament_id = intval($_POST['tournament_id']);
  $tournament_name = sanitize_text_field($_POST['tournament_name']);
  $tournament_days = sanitize_text_field($_POST['tournament_days']);
  $tournament_hours = $_POST['tournament_hours'];
  $tournament_fields_5v5 = intval($_POST['tournament_fields_5v5']);
  $tournament_fields_7v7 = intval($_POST['tournament_fields_7v7']);

  $result = TournamentsDatabase::update_tournament($tournament_id, $tournament_name, $tournament_days, $tournament_fields_5v5, $tournament_fields_7v7);
  if ($result['success']) {
    TournamentHoursDatabase::delete_tournament_hours_by_tournament($tournament_id);
    $days = explode(',', $tournament_days);
    $days = array_map('trim', $days);
    foreach ($days as $index => $day) {
      $hours = $tournament_hours[$index];
      TournamentHoursDatabase::insert_tournament_hours($tournament_id, $day, intval($hours[0]), intval($hours[1]));
    }
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    wp_send_json_success(['message' => 'Torneo actualizado correctamente', 'html' => on_add_tournament($tournament)]);
  }
  wp_send_json_error(['message' => 'Torneo no actualizado, torneo ya existe']);
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
    'divisions' => cuicpro_divisions($tournament), 
    'coaches' => cuicpro_coaches_table($tournament), 
    'brackets' => generate_brackets_dropdown($tournament),
    'officials' => render_officials($tournament),
    'teams' => cuicpro_teams_by_coach($tournament) . cuicpro_players_by_team(),
    'teams_by_division' => cuicpro_teams_by_division($tournament),
    'tournament_days' => $tournament->tournament_days,
    'official_hours' => create_hours_select_input($tournament_hours),
    'matches' => cuicpro_matches($tournament)
    ]);
}

function get_tournament_days() {
  $active_tournament = TournamentsDatabase::get_active_tournament();
  wp_send_json_success(['days' => $active_tournament->tournament_days]);
}

function set_officials_to_matches($tournament_id){
  $matches = PendingMatchesDatabase::get_matches_by_tournament($tournament_id);
  $officials = OfficialsDatabase::get_officials_by_tournament($tournament_id);

  $certified_officials = array_filter($officials, function($official) {
    return $official->official_is_certified;
  });

  $uncertified_officials = array_filter($officials, function($official) {
    return !$official->official_is_certified;
  });
  
  shuffle($certified_officials);
  shuffle($uncertified_officials);

  foreach ($matches as $match) {
    $mode = DivisionsDatabase::get_division_by_id($match->division_id)->division_mode;
    // If official is already assigned, skip
    if ($match->official_id) continue;
    
    $is_assigned = false;
    
    foreach ($certified_officials as $official) {
      // if tournament mode is not the same as official mode, skip
      if ($official->official_mode != $mode && $official->official_mode != 3) {
        continue; 
      }
      
      $match_time = $match->match_time;
      $match_date = $match->match_date;
      $official_hours = OfficialsHoursDatabase::get_official_hours_by_day($official->official_id, $match_date);

      // if not available this day, skip
      if (!$official_hours) {
        continue;
      }
      // check if official is available at match time
      if ( !str_contains($official_hours->official_available_hours, $match_time) ) {
        continue;
      }

      
      $new_official_hours = explode(',', $official_hours->official_available_hours);
      $new_official_hours = array_filter($new_official_hours, function($hour) use ($match_time) {
        return $hour != $match_time;
      });
      $new_official_hours = implode(',', $new_official_hours);
      
      PendingMatchesDatabase::update_match_official($match->match_id, $official->official_id);
      OfficialsHoursDatabase::update_official_available_hours($official_hours->official_hours_id, $new_official_hours);
      $is_assigned = true;
      break;
    }

    if (!$is_assigned) {
      foreach ($uncertified_officials as $official) {
        // if tournament mode is not the same as official mode, skip
        if ($official->official_mode != $mode && $official->official_mode != 3) {
          continue; 
        }
        
        $match_time = $match->match_time;
        $match_date = $match->match_date;
        $official_hours = OfficialsHoursDatabase::get_official_hours_by_day($official->official_id, $match_date);

        // if not available this day, skip
        if (!$official_hours) {
          continue;
        }
        // check if official is available at match time
        if ( !str_contains($official_hours->official_available_hours, $match_time) ) {
          continue;
        }

        $new_official_hours = explode(',', $official_hours->official_available_hours);
        $new_official_hours = array_filter($new_official_hours, function($hour) use ($match_time) {
          return $hour != $match_time;
        });
        $new_official_hours = implode(',', $new_official_hours);
        
        PendingMatchesDatabase::update_match_official($match->match_id, $official->official_id);
        OfficialsHoursDatabase::update_official_available_hours($official_hours->official_hours_id, $new_official_hours);
        break;
      }
    }
  }
}

function unset_officials_from_matches(int $tournament_id) {
  $officials = OfficialsDatabase::get_officials_by_tournament($tournament_id);
  foreach ($officials as $official) {
    $official_hours = OfficialsHoursDatabase::get_official_hours($official->official_id);
    if (!$official_hours) {
      continue;
    }
    foreach ($official_hours as $official_hour) {
      OfficialsHoursDatabase::reset_official_available_hours($official_hour->official_hours_id, $official_hour->official_hours);
    }
  }

  return true;
}

function assign_officials() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  if (!$tournament) {
    wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
  }

  set_officials_to_matches($tournament_id);

  TournamentsDatabase::update_tournament_has_officials($tournament_id, true);

  wp_send_json_success(['message' => 'Arbitros asignados correctamente']);
}

function unassign_officials() {
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  if (!$tournament) {
    wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
  }

  $result = unset_officials_from_matches($tournament_id);
  TournamentsDatabase::update_tournament_has_officials($tournament_id, false);

  wp_send_json_success(['message' => 'Arbitros desasignados correctamente', 'result' => $result]);
}

add_action('wp_ajax_create_general_tournament', 'create_general_tournament');
add_action('wp_ajax_nopriv_create_general_tournament', 'create_general_tournament');
add_action('wp_ajax_edit_tournament', 'edit_tournament');
add_action('wp_ajax_nopriv_edit_tournament', 'edit_tournament');
add_action('wp_ajax_update_tournament', 'update_tournament');
add_action('wp_ajax_nopriv_update_tournament', 'update_tournament');
add_action('wp_ajax_unassign_officials', 'unassign_officials');
add_action('wp_ajax_nopriv_unassign_officials', 'unassign_officials');
add_action('wp_ajax_assign_officials', 'assign_officials');
add_action('wp_ajax_nopriv_assign_officials', 'assign_officials');
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
