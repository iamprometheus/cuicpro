<?php

function create_bracket_match($match) {
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  $official = OfficialsDatabase::get_official_by_id($match->official_id);
  $official_name = $official ? $official->official_name : "Asignacion Pendiente";

  $match_time = $match->match_time . ":00";

  // if ($match->match_link_1) {
  //   $previous_match_info = PendingMatchesDatabase::get_match_by_bracket_match($match->match_link_1, $match->bracket_id);
  //   $selected_1 = $match->team_id_1 != null && $match->team_id_1 == $previous_match_info->team_id_1 ? "selected" : "";
  //   $selected_2 = $match->team_id_1 != null && $match->team_id_1 == $previous_match_info->team_id_2 ? "selected" : "";

  //   $team_1_name = "<select id='team-winner' data-match-id='" . $match->match_id . "' data-prev-match='" . $previous_match_info->match_id . "' data-team-number='1' >";
  //   $team_1_name .= "<option value='0'>Seleccionar Ganador</option>";
  //   $team_1_name .= "<option value='" . $previous_match_info->team_id_1 . "' $selected_1>" . TeamsDatabase::get_team_by_id($previous_match_info->team_id_1)->team_name . "</option>";
  //   $team_1_name .= "<option value='" . $previous_match_info->team_id_2 . "' $selected_2>" . TeamsDatabase::get_team_by_id($previous_match_info->team_id_2)->team_name . "</option>";
  //   $team_1_name .= "</select>";
  // } 

  // if ($match->match_link_2) {
  //   $previous_match_info = PendingMatchesDatabase::get_match_by_bracket_match($match->match_link_2, $match->bracket_id);
  //   $selected_1 = $match->team_id_2 != null && $match->team_id_2 == $previous_match_info->team_id_1 ? "selected" : "";
  //   $selected_2 = $match->team_id_2 != null && $match->team_id_2 == $previous_match_info->team_id_2 ? "selected" : "";

  //   $team_2_name = "<select id='team-winner' data-match-id='" . $match->match_id . "' data-prev-match='" . $previous_match_info->match_id . "' data-team-number='2' >";
  //   $team_2_name .= "<option value='0'>Seleccionar Ganador</option>";
  //   $team_2_name .= "<option value='" . $previous_match_info->team_id_1 . "' $selected_1>" . TeamsDatabase::get_team_by_id($previous_match_info->team_id_1)->team_name . "</option>";
  //   $team_2_name .= "<option value='" . $previous_match_info->team_id_2 . "' $selected_2>" . TeamsDatabase::get_team_by_id($previous_match_info->team_id_2)->team_name . "</option>";
  //   $team_2_name .= "</select>";
  // }

  $html = "<div class='bracket-match'>";
  $html .= "<span>" . $team_1_name . "</span>";
  $html .= "<span>VS</span>";
  $html .= "<span class='text-right'>" . $team_2_name . "</span>";
  $html .= "</div>";
  $html .= "<hr />";
  $html .= "<div class='match-data-container'>";
  $html .= "<div class='match-data'>";
  $html .= "<span>Fecha: " . $match->match_date . "</span>";
  $html .= "<span>Hora: " . $match_time . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data text-right'>";
  $html .= "<span>Arbitro: " . $official_name . "</span>";
  $html .= "<span>Campo: " . $match->field_number . "</span>";
  $html .= "</div>";
  $html .= "</div>";
  
  $html .= "<hr />";

  if (!$match->match_pending) {
    $winner = MatchesDatabase::get_match_by_pending_match_id($match->match_id)->match_winner;
    $html .= "<div class='match-data-end-data'>";
    $html .= "<span style='font-weight: bold; font-size: 16px;'>Resultados</span>";
    $html .= "<span> Ganador: " . TeamsDatabase::get_team_by_id($winner)->team_name . " </span>";
    $html .= "</div>";

    return $html;
  }
  
  if ($team_1_name !== "TBD" && $team_2_name !== "TBD") {
    $html .= "<div class='match-data-end-data'>";
    $html .= "<span style='font-weight: bold; font-size: 16px;'>Resultados</span>";
    $html .= "<div class='score-select'>";
    $html .= "<span>Goles equipo $team_1_name : </span>";
    $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_1 . "' id='team-1-score-" . $match->match_id . "' value=0 />";
    $html .= "</div>";
    $html .= "<div class='score-select'>";
    $html .= "<span>Goles equipo $team_2_name : </span>";
    $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_2 . "' id='team-2-score-" . $match->match_id . "' value=0 />";
    $html .= "</div>";
   
    $html .= "<div class='winner-select'>";
    $html .= "<span>Ganador: </span>";
    $html .= "<select id='team-winner-" . $match->match_id . "'>";
    $html .= "<option value='-1'>Seleccionar Ganador</option>";
    $html .= "<option value='" . $match->team_id_1 . "'>" . $team_1_name . "</option>";
    $html .= "<option value='" . $match->team_id_2 . "'>" . $team_2_name . "</option>";
    $html .= "</select>";
    $html .= "</div>";
   
    $html .= "<button id='save-match-single-elimination' data-match-id='" . $match->match_id . "'>Guardar Resultado</button>";
    $html .= "</div>";
  }
  return $html;
}

function create_single_elimination_bracket(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_matches_by_bracket($bracket_id);

  $bracket_rounds = array_unique(array_map(function($match) {
    return $match->bracket_round;
  }, $matches));

  $html = "<div class='bracket-container'>";
  $elements = [];
  foreach ($bracket_rounds as $round) {
    $html .= "<div id='round_" . $round . "' class='bracket-round'>";

    $elements[$round] = [];
    foreach ($matches as $match) {
      if ($match->bracket_round == $round) {
        $previous_match_1 = $match->match_link_1;
        $previous_match_2 = $match->match_link_2;

        if ($previous_match_1) {
          $previous_match_1_info = PendingMatchesDatabase::get_match_by_bracket_match($previous_match_1, $match->bracket_id);
          $elements[$round]["match_" . $match->match_id][] = "match_" . $previous_match_1_info->match_id;
        }

        if ($previous_match_2) {
          $previous_match_2_info = PendingMatchesDatabase::get_match_by_bracket_match($previous_match_2, $match->bracket_id);
          $elements[$round]["match_" . $match->match_id][] = "match_" . $previous_match_2_info->match_id;
        }

        $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
        $html .= create_bracket_match($match);
        $html .= "</div>";
      }
    }
    $html .= "</div>";
  }

  $html .= "</div>";
  return ['html' => $html, 'elements' => $elements, 'matches' => $matches];
}

function render_round_robin_match($match) {
  $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;

  $official = OfficialsDatabase::get_official_by_id($match->official_id);
  $official_name = $official ? $official->official_name : "Asignacion Pendiente";

  $match_time = $match->match_time . ":00";

  $html = "<div class='bracket-match'>";
  $html .= "<span>" . $team_1_name . "</span>";
  $html .= "<span>VS</span>";
  $html .= "<span>" . $team_2_name . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data-container'>";
  $html .= "<div class='match-data'>";
  $html .= "<span>Fecha: " . $match->match_date . "</span>";
  $html .= "<span>Hora: " . $match_time . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data text-right'>";
  $html .= "<span>Arbitro: " . $official_name . "</span>";
  $html .= "<span>Campo: " . $match->field_number . "</span>";
  $html .= "</div>";
  $html .= "</div>";
  $html .= "<div class='match-data-end-data'>";
  $html .= "<hr/>";
  $html .= "<span style='font-weight: bold; font-size: 16px;'>Resultados</span>";
  $html .= "<span>Ganador: </span>";
  $html .= "<select id='team-winner-" . $match->match_id . "'>";
  $html .= "<option value='-1'>Seleccionar Ganador</option>";
  $html .= "<option value='" . $match->team_id_1 . "'>" . $team_1_name . "</option>";
  $html .= "<option value='" . $match->team_id_2 . "'>" . $team_2_name . "</option>";
  $html .= "<option value='0'>Empate</option>";
  $html .= "</select>";
  $html .= "<span>Goles equipo $team_1_name : </span>";
  $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_1 . "' id='team-1-score-" . $match->match_id . "' value=0 />";
  $html .= "<span>Goles equipo $team_2_name : </span>";
  $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_2 . "' id='team-2-score-" . $match->match_id . "' value=0 />";
  $html .= "<button id='save-match' data-match-id='" . $match->match_id . "'>Guardar Resultado</button>";
  $html .= "</div>";
  return $html;
}

function render_leaderboard_table(int $bracket_id) {
  $bracket = BracketsDatabase::get_bracket_by_id($bracket_id);
  $division = $bracket->division_id;
  $teams = TeamsDatabase::get_enrolled_teams_by_division($division);

  usort($teams, function($a, $b) {
    return $b->team_points - $a->team_points;
  });

  $html = "<table class='leaderboard-table' border='1' align='center'>";
  $html .= "<caption>Tabla de lideres</caption>";
  $html .= "<thead>";
  $html .= "<tr>";
  $html .= "<th>Posición</th>";
  $html .= "<th>Equipo</th>";
  $html .= "<th>PJ</th>";
  $html .= "<th>PG</th>";
  $html .= "<th>PP</th>";
  $html .= "<th>PE</th>";
  $html .= "<th>GF</th>";
  $html .= "<th>GC</th>";
  $html .= "<th>DG</th>";
  $html .= "<th>Puntos</th>";
  $html .= "</tr>";
  $html .= "</thead>";
  $html .= "<tbody>";
  foreach ($teams as $index => $team) {
    $matches = MatchesDatabase::get_matches_by_team($team->team_id, $bracket->tournament_id);

    $pj = count($matches);
    $pg = count(array_filter($matches, function($match) use ($team) {
      return $match->match_winner == $team->team_id;
    }));
    $pp = count(array_filter($matches, function($match) use ($team) {
      return $match->match_winner != $team->team_id;
    }));
    $pe = count(array_filter($matches, function($match) {
      return $match->match_winner == null;
    }));
    $gf = MatchesDatabase::get_goals_in_favor_by_team($team->team_id);
    $gc = MatchesDatabase::get_goals_against_by_team($team->team_id);

    $dg = $gf - $gc;
    $pts = $team->team_points;

    foreach ($matches as $match) {
      if ($match->team_id_1 == $team->team_id) {
        $pts += $match->goals_team_1 > $match->goals_team_2 ? 3 : 0;
        $pts += $match->goals_team_1 == $match->goals_team_2 ? 1 : 0;
      }
      if ($match->team_id_2 == $team->team_id) {
        $pts += $match->goals_team_2 > $match->goals_team_1 ? 3 : 0;
        $pts += $match->goals_team_2 == $match->goals_team_1 ? 1 : 0;
      }
    }

    $html .= "<tr>";
    $html .= "<td>" . $index + 1 . "</td>";
    $html .= "<td>" . $team->team_name . "</td>";
    $html .= "<td>" . $pj . "</td>";
    $html .= "<td>" . $pg . "</td>";
    $html .= "<td>" . $pp . "</td>";
    $html .= "<td>" . $pe . "</td>";
    $html .= "<td>" . $gf . "</td>";
    $html .= "<td>" . $gc . "</td>";
    $html .= "<td>" . $dg . "</td>";
    $html .= "<td>" . $pts . "</td>";
    $html .= "</tr>";
  }
  $html .= "</tbody>";
  $html .= "</table>";

  return $html;
}

function create_round_robin_bracket(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_pending_matches_by_bracket($bracket_id);

  $days = array_unique(array_map(function($match) {
    return $match->match_date;
  }, $matches));

  $html = "<div class='matches-container'>";
  $html .= "<div class='leaderboard-container'>";
  $html .= render_leaderboard_table($bracket_id);
  $html .= "</div>";
  $elements = [];
  foreach ($days as $day) {
    $html .= "<hr/>";
    $html .= "<div class='day-title'> Partidos del dia: " . $day . "</div>";
    $html .= "<div id='day_" . $day . "' class='day-container'>";

    foreach ($matches as $match) {
      if ($match->match_date == $day) {
        $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
        $html .= render_round_robin_match($match);
        $html .= "</div>";
      }
    }
    $html .= "</div>";
  }

  $html .= "</div>";
  return ['html' => $html, 'elements' => $elements, 'matches' => $matches];
}

function on_fetch_bracket_data(int $bracket_id) {

  $bracket = BracketsDatabase::get_bracket_by_id($bracket_id);
  $tournament = TournamentsDatabase::get_tournament_by_id($bracket->tournament_id);

  if ($tournament->tournament_type == 1) {
    return create_single_elimination_bracket($bracket_id);
  }

  if ($tournament->tournament_type == 2) {
    return create_round_robin_bracket($bracket_id);
  }
}

function fetch_bracket_data() {
  if (!isset($_POST['bracket_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $bracket_id = intval($_POST['bracket_id']);

  wp_send_json_success(['message' => 'Bracket recuperado correctamente', 'html' => on_fetch_bracket_data($bracket_id)['html'], 'elements' => on_fetch_bracket_data($bracket_id)['elements'], 'matches' => on_fetch_bracket_data($bracket_id)['matches']]);
}

function update_match_winner_single_elimination() {
  if (!isset($_POST['match_id']) || !isset($_POST['match_winner']) || !isset($_POST['team_1_score']) || !isset($_POST['team_2_score'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $match_id = intval($_POST['match_id']);
  $match_winner = intval($_POST['match_winner']);
  $team_1_score = intval($_POST['team_1_score']);
  $team_2_score = intval($_POST['team_2_score']);

  $match_data = PendingMatchesDatabase::get_match_by_id($match_id);
  $match_link = $match_data->bracket_match;
  $bracket_id = $match_data->bracket_id;

  $total_matches = count(PendingMatchesDatabase::get_matches_by_bracket($bracket_id));

  if ($total_matches != $match_link) { 
    $match_link_data = PendingMatchesDatabase::get_match_by_match_link($bracket_id, $match_link);
    if ($match_link_data->match_link_1 == $match_link) {
      PendingMatchesDatabase::update_match_team_1($match_link_data->match_id, $match_winner);
    } else {
      PendingMatchesDatabase::update_match_team_2($match_link_data->match_id, $match_winner);
    }
  }
    
  $result = MatchesDatabase::insert_match(
      $match_data->tournament_id, 
      $match_data->division_id, 
      $match_data->bracket_id, 
      $match_data->bracket_round,
      $match_data->bracket_match, 
      $match_data->field_number, 
      $match_data->team_id_1, 
      $match_data->team_id_2, 
      $match_data->official_id === 0 ? null : $match_data->official_id,
      $match_data->match_date, 
      $match_data->match_time, 
      $team_1_score,
      $team_2_score,
      $match_winner,
      $match_id);
  
  if ($result) {
    PendingMatchesDatabase::end_match($match_id);
    $match_winner_name = TeamsDatabase::get_team_by_id($match_winner)->team_name;
    $html = "<span style='font-weight: bold; font-size: 16px;'>Resultados</span><span> Ganador: $match_winner_name </span>";

    wp_send_json_success(['message' => 'Ganador actualizado correctamente', 'html' => $html]);
  }

  // if(!$result) {
  //   $match_id = MatchesDatabase::get_match_by_bracket_match($prev_match_info->bracket_match, $prev_match_info->bracket_id)->match_id;
  //   $result = MatchesDatabase::update_match_winner(
  //     $match_id, 
  //     $team_id);
  // }

  wp_send_json_success(['message' => 'Ganador actualizado correctamente']);
}

function update_match_winner_round_robin() {
  if (!isset($_POST['match_id']) || !isset($_POST['match_winner']) || !isset($_POST['team_1_score']) || !isset($_POST['team_2_score'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $match_id = intval($_POST['match_id']);
  $match_winner = intval($_POST['match_winner']) === 0 ? null : intval($_POST['match_winner']);
  $team_1_score = intval($_POST['team_1_score']);
  $team_2_score = intval($_POST['team_2_score']);

  $prev_match_info = PendingMatchesDatabase::get_match_by_id($match_id);
  
  $result = MatchesDatabase::insert_match(
      $prev_match_info->tournament_id, 
      $prev_match_info->division_id, 
      $prev_match_info->bracket_id, 
      $prev_match_info->bracket_round,
      $prev_match_info->bracket_match, 
      $prev_match_info->field_number, 
      $prev_match_info->team_id_1, 
      $prev_match_info->team_id_2, 
      $prev_match_info->official_id === 0 ? null : $prev_match_info->official_id,
      $prev_match_info->match_date, 
      $prev_match_info->match_time, 
      $team_1_score,
      $team_2_score,
      $match_winner,
      $match_id);
  
  if ($result) {
    PendingMatchesDatabase::end_match($match_id);
    wp_send_json_success(['message' => 'Ganador actualizado correctamente']);
  }

  wp_send_json_error(['message' => 'Error al actualizar el ganador']);
}

add_action('wp_ajax_update_match_winner_round_robin', 'update_match_winner_round_robin');
add_action('wp_ajax_nopriv_update_match_winner_round_robin', 'update_match_winner_round_robin');
add_action('wp_ajax_update_match_winner_single_elimination', 'update_match_winner_single_elimination');
add_action('wp_ajax_nopriv_update_match_winner_single_elimination', 'update_match_winner_single_elimination');
add_action('wp_ajax_fetch_bracket_data', 'fetch_bracket_data');
add_action('wp_ajax_nopriv_fetch_bracket_data', 'fetch_bracket_data');
