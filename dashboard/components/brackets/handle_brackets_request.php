<?php

function render_available_officials($match) {
  $officials = OfficialsDatabase::get_officials_by_tournament($match->tournament_id);

  $html = "";
  $html .= "<select id='match-official-select' data-match-id='" . $match->match_id . "'>";

  if ($match->official_id) {
    $html .= "<option value='" . $match->official_id . "'>" . OfficialsDatabase::get_official_by_id($match->official_id)->official_name . "</option>";
  } else {
    $html .= "<option value='0'>Arbitro no asignado aun</option>";
  }

  foreach ($officials as $official) {
    $official_hours = OfficialsHoursDatabase::get_official_hours_by_day($official->official_id, $match->match_date)->official_available_hours;

    if (!$official_hours) {
      continue;
    }

    $is_available = str_contains($official_hours, $match->match_time);
    if (!$is_available) {
      continue;
    }
    
    $html .= "<option value='" . $official->official_id . "'>" . $official->official_name . "</option>";
  }
  $html .= "</select>";
  return $html;
}

function create_bracket_match($match) {
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  $match_time = $match->match_time . ":00";

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
  $html .= "<span>Arbitro: " . render_available_officials($match) . "</span>";
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
    $html .= "<span>Anotaciones equipo $team_1_name : </span>";
    $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_1 . "' id='team-1-score-" . $match->match_id . "' value=0 />";
    $html .= "</div>";
    $html .= "<div class='score-select'>";
    $html .= "<span>Anotaciones equipo $team_2_name : </span>";
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

function create_playoffs_bracket(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_matches_by_bracket($bracket_id);

  $playoffs_id = array_unique(array_map(function($match) {
    return $match->playoff_id;
  }, $matches));

  $elements = [];
  $html = "";
  $html .= "<div class='playoffs-container'>";
  $html .= "<hr />";
  $html .= "<span style='font-weight: bold; font-size: 40px; text-align: center;'>Playoffs</span>";
  foreach ($playoffs_id as $playoff_id) {
    if (!$playoff_id) continue;
    $bracket_matches = array_filter($matches, function($match) use ($playoff_id) {
      return $match->playoff_id == $playoff_id;
    });

    $bracket_rounds = array_unique(array_map(function($match) {
      return $match->bracket_round;
    }, $bracket_matches));

    $html .= "<div class='bracket-container' id='playoff_" . $playoff_id . "'>";
    $html .= "<span style='font-weight: bold; font-size: 22px; text-align: center;'>Playoffs #" . $playoff_id . "</span>";
    $html .= "<hr />";
    $html .= "<div class='rounds-container'>";
    $elements[$playoff_id] = [];
    foreach ($bracket_rounds as $round) {
      $html .= "<div id='round_" . $round . "' class='bracket-round'>";

      $elements[$playoff_id][$round] = [];
      foreach ($bracket_matches as $match) {
        if ($match->bracket_round == $round) {
          $previous_match_1 = $match->match_link_1;
          $previous_match_2 = $match->match_link_2;

          if ($previous_match_1) {
            $previous_match_1_info = PendingMatchesDatabase::get_match_by_bracket_match_and_playoff($previous_match_1, $match->bracket_id, $playoff_id);
            $elements[$playoff_id][$round]["match_playoff_" . $match->match_id][] = "match_playoff_" . $previous_match_1_info->match_id;
          }

          if ($previous_match_2) {
            $previous_match_2_info = PendingMatchesDatabase::get_match_by_bracket_match_and_playoff($previous_match_2, $match->bracket_id, $playoff_id);
            $elements[$playoff_id][$round]["match_playoff_" . $match->match_id][] = "match_playoff_" . $previous_match_2_info->match_id;
          }

          $html .= "<div id='match_playoff_" . $match->match_id . "' class='bracket-match-container'>";
          $html .= create_bracket_match($match);
          $html .= "</div>";
        }
      }
      $html .= "</div>";
    }
    $html .= "</div>";
    $html .= "</div>";
  }
  $html .= "</div>";

  return ['html' => $html, 'elements' => $elements, 'matches' => $matches];
}

function render_round_robin_match($match) {
  $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;

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
  $html .= "<span>Arbitro: " . render_available_officials($match) . "</span>";
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
  $html .= "<span>Anotaciones equipo $team_1_name : </span>";
  $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_1 . "' id='team-1-score-" . $match->match_id . "' value=0 />";
  $html .= "<span>Anotaciones equipo $team_2_name : </span>";
  $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_2 . "' id='team-2-score-" . $match->match_id . "' value=0 />";
  $html .= "<button id='save-match' data-match-id='" . $match->match_id . "'>Guardar Resultado</button>";
  $html .= "</div>";
  return $html;
}

function render_playoff_match($match) {
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

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
  $html .= "<span>Arbitro: " . render_available_officials($match) . "</span>";
  $html .= "<span>Campo: " . $match->field_number . "</span>";
  $html .= "</div>";
  $html .= "</div>";
  $html .= "<div class='match-data-end-data'>";
  $html .= "<hr/>";
  if ($match->team_id_1 && $match->team_id_2) {
    $html .= "<span style='font-weight: bold; font-size: 16px;'>Resultados</span>";
    $html .= "<span>Ganador: </span>";
    $html .= "<select id='team-winner-" . $match->match_id . "'>";
    $html .= "<option value='-1'>Seleccionar Ganador</option>";
    $html .= "<option value='" . $match->team_id_1 . "'>" . $team_1_name . "</option>";
    $html .= "<option value='" . $match->team_id_2 . "'>" . $team_2_name . "</option>";
    $html .= "<option value='0'>Empate</option>";
    $html .= "</select>";
    $html .= "<span>Anotaciones equipo $team_1_name : </span>";
    $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_1 . "' id='team-1-score-" . $match->match_id . "' value=0 />";
    $html .= "<span>Anotaciones equipo $team_2_name : </span>";
    $html .= "<input type='number' min='0' data-team-id='" . $match->team_id_2 . "' id='team-2-score-" . $match->match_id . "' value=0 />";
    $html .= "<button id='save-match' data-match-id='" . $match->match_id . "'>Guardar Resultado</button>";
  }
  else {
    $html .= "<span style='font-weight: bold; font-size: 16px;'>Pendiente resultado de partidos anteriores</span>";
  }
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
  $html .= "<th>AF</th>";
  $html .= "<th>AC</th>";
  $html .= "<th>DA</th>";
  $html .= "<th>PA</th>";
  $html .= "<th>Puntos</th>";
  $html .= "</tr>";
  $html .= "</thead>";
  $html .= "<tbody>";

  $data = [];
  foreach ($teams as $index => $team) {
    $matches = MatchesDatabase::get_matches_by_team($team->team_id, $bracket->tournament_id);

    $matches = array_filter($matches, function($match) {
      return $match->match_type == 1;
    });

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

    $pa = $pj > 0 ? $gf / $pj : 0;

    $data[] = [
      "team" => $team->team_name,
      "pj" => $pj,
      "pg" => $pg,
      "pp" => $pp,
      "pe" => $pe,
      "gf" => $gf,
      "gc" => $gc,
      "dg" => $dg,
      "pa" => $pa,
      "pts" => $pts
    ];

  }

  usort($data, function($a, $b) {
    return $b['pts'] - $a['pts'];
  });

  foreach ($data as $index => $team) {
    $html .= "<tr>";
    $html .= "<td>" . $index + 1 . "</td>";
    $html .= "<td>" . $team['team'] . "</td>";
    $html .= "<td>" . $team['pj'] . "</td>";
    $html .= "<td>" . $team['pg'] . "</td>";
    $html .= "<td>" . $team['pp'] . "</td>";
    $html .= "<td>" . $team['pe'] . "</td>";
    $html .= "<td>" . $team['gf'] . "</td>";
    $html .= "<td>" . $team['gc'] . "</td>";
    $html .= "<td>" . $team['dg'] . "</td>";
    $html .= "<td>" . $team['pa'] . "</td>";
    $html .= "<td>" . $team['pts'] . "</td>";
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

function create_mixed_bracket(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_pending_matches_by_bracket($bracket_id);

  $days = array_unique(array_map(function($match) {
    return $match->match_date;
  }, $matches));
  
  $html = "<div class='matches-container'>";
  $html .= "<div class='leaderboard-container'>";
  $html .= render_leaderboard_table($bracket_id);
  $html .= "</div>";
  
  foreach ($days as $day) {
    $html .= "<hr/>";
    $html .= "<div class='day-title'> Partidos del dia: " . $day . "</div>";
    $html .= "<div id='day_" . $day . "' class='day-container'>";

    foreach ($matches as $match) {
      if ($match->match_date == $day) {
        if ($match->match_type == 2) continue;

        $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
        $html .= render_round_robin_match($match);
        $html .= "</div>";
      }
    }
    $html .= "</div>";
  }

  $playoffs = create_playoffs_bracket($bracket_id);
  $html .= $playoffs['html'];

  $html .= "</div>";
  return ['html' => $html, 'elements' => $playoffs['elements'], 'matches' => $matches];
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

  if ($tournament->tournament_type == 3) {
    return create_mixed_bracket($bracket_id);
  }
}

function fetch_bracket_data() {
  if (!isset($_POST['bracket_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $bracket_id = intval($_POST['bracket_id']);
  wp_send_json_success(['message' => 'Bracket recuperado correctamente', 'html' => on_fetch_bracket_data($bracket_id)['html'], 'elements' => on_fetch_bracket_data($bracket_id)['elements']]);
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
      $match_data->field_type,
      $match_data->team_id_1, 
      $match_data->team_id_2, 
      $match_data->official_id === 0 ? null : $match_data->official_id,
      $match_data->match_date, 
      $match_data->match_time, 
      $match_data->match_type,
      $match_data->playoff_id,
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
      $prev_match_info->field_type,
      $prev_match_info->official_id === 0 ? null : $prev_match_info->official_id,
      $prev_match_info->match_date, 
      $prev_match_info->match_time, 
      $prev_match_info->match_type,
      $prev_match_info->playoff_id,
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

function handle_switch_assigned_official($match_id, $new_official_id) {
  $match = PendingMatchesDatabase::get_match_by_id($match_id);
  if (strval($match->official_id) == strval($new_official_id)) {
    return true;
  }

  // if match has no official assigned
  if (!$match->official_id) {
    PendingMatchesDatabase::update_match_official($match_id, $new_official_id);
    return true;
  }

  // if match has official assigned add and remove corresponding hour to officials available hours
  // get officials
  $official = OfficialsDatabase::get_official_by_id($match->official_id);
  $new_official = OfficialsDatabase::get_official_by_id($new_official_id);
  
  // update match official
  PendingMatchesDatabase::update_match_official($match_id, $new_official_id);
  
  // get official and new official hours for the day of the match
  $official_hours = OfficialsHoursDatabase::get_official_hours_by_day($official->official_id, $match->match_date);
  $new_official_hours = OfficialsHoursDatabase::get_official_hours_by_day($new_official->official_id, $match->match_date);

  // add match hour to official available hours
  $official_new_hours = explode(",", $official_hours->official_available_hours);
  $official_new_hours[] = $match->match_time;
  $official_new_hours = implode(",", $official_new_hours);
  OfficialsHoursDatabase::update_official_available_hours($official_hours->official_hours_id, $official_new_hours);

  // remove match hour from new official available hours
  $new_official_new_hours = explode(",", $new_official_hours->official_available_hours);
  $new_official_new_hours = array_diff($new_official_new_hours, [$match->match_time]);
  $new_official_new_hours = implode(",", $new_official_new_hours);
  OfficialsHoursDatabase::update_official_available_hours($new_official_hours->official_hours_id, $new_official_new_hours);

  return true;
}

function switch_assigned_official() {
  if (!isset($_POST['match_id']) || !isset($_POST['official_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $match_id = intval($_POST['match_id']);
  $official_id = intval($_POST['official_id']);

  $result = handle_switch_assigned_official($match_id, $official_id);

  if ($result) {
    wp_send_json_success(['message' => 'Arbitro actualizado correctamente']);
  }

  wp_send_json_error(['message' => 'Error al actualizar el arbitro']);
}

add_action('wp_ajax_switch_assigned_official', 'switch_assigned_official');
add_action('wp_ajax_nopriv_switch_assigned_official', 'switch_assigned_official');
add_action('wp_ajax_update_match_winner_round_robin', 'update_match_winner_round_robin');
add_action('wp_ajax_nopriv_update_match_winner_round_robin', 'update_match_winner_round_robin');
add_action('wp_ajax_update_match_winner_single_elimination', 'update_match_winner_single_elimination');
add_action('wp_ajax_nopriv_update_match_winner_single_elimination', 'update_match_winner_single_elimination');
add_action('wp_ajax_fetch_bracket_data', 'fetch_bracket_data');
add_action('wp_ajax_nopriv_fetch_bracket_data', 'fetch_bracket_data');
