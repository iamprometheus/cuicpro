<?php

function render_brackets_fe_response($active_tournament) {
  $html = "";
  if (!$active_tournament) {
    $html .= "<h3>No hay brackets para mostrar</h3>";
    return $html;
  }
  $brackets = BracketsDatabase::get_brackets_by_tournament($active_tournament->tournament_id);

  $html .= "<div class='brackets-list-container'>";
  if (empty($brackets)) {
    $html .= "<h3>No hay brackets para mostrar</h3>";
    $html .= "</div>";
    return $html;
  }
  foreach ($brackets as $bracket) {
    $division = DivisionsDatabase::get_division_by_id(intval($bracket->division_id));
    $mode = ModesDatabase::get_mode_by_id($division->division_mode);
    $category = CategoriesDatabase::get_category_by_id($division->division_category);
    
    $html .= 
    "<div class='brackets-list-item' data-bracket-id='" . $bracket->bracket_id . "'>
      <span>" . $division->division_name . " " . $mode->mode_description. " " . $category->category_description . "</span>
    </div>";
  }
  $html .= "</div>";
  return $html;
}

function create_bracket_match_fe($match) {
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  $match_time = $match->match_time . ":00";

  $official = OfficialsDatabase::get_official_by_id($match->official_id);
  $official_name = $official->official_name ? $official->official_name : "Asignacion Pendiente";

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
  
  return $html;
}

function create_single_elimination_bracket_fe(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_matches_by_bracket($bracket_id);

  $bracket_rounds = array_unique(array_map(function($match) {
    return $match->bracket_round;
  }, $matches));

  $html = "";
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
        $html .= create_bracket_match_fe($match);
        $html .= "</div>";
      }
    }
    $html .= "</div>";
  }

  return ['html' => $html, 'elements' => $elements, 'matches' => $matches];
}

function render_round_robin_match_fe($match) {
  $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;

  $match_time = $match->match_time . ":00";
  $match_official = OfficialsDatabase::get_official_by_id($match->official_id);
  $official_name = $match_official->official_name ? $match_official->official_name : "Asignacion Pendiente";

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
  return $html;
}

function render_leaderboard_table_fe(int $bracket_id) {
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

function create_round_robin_bracket_fe(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_pending_matches_by_bracket($bracket_id);

  $days = array_unique(array_map(function($match) {
    return $match->match_date;
  }, $matches));

  $html = "<div class='matches-container'>";
  $html .= "<div class='leaderboard-container'>";
  $html .= render_leaderboard_table_fe($bracket_id);
  $html .= "</div>";
  $elements = [];
  foreach ($days as $day) {
    $html .= "<hr/>";
    $html .= "<div class='day-title'> Partidos del dia: " . $day . "</div>";
    $html .= "<div id='day_" . $day . "' class='day-container'>";

    foreach ($matches as $match) {
      if ($match->match_date == $day) {
        $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
        $html .= render_round_robin_match_fe($match);
        $html .= "</div>";
      }
    }
    $html .= "</div>";
  }

  $html .= "</div>";
  return ['html' => $html, 'elements' => $elements, 'matches' => $matches];
}

function on_fetch_bracket_data_fe(int $bracket_id) {
  $bracket = BracketsDatabase::get_bracket_by_id($bracket_id);
  $tournament = TournamentsDatabase::get_tournament_by_id($bracket->tournament_id);

  if ($tournament->tournament_type == 1) {
    return create_single_elimination_bracket_fe($bracket_id);
  }

  if ($tournament->tournament_type == 2) {
    return create_round_robin_bracket_fe($bracket_id);
  }
}

function fetch_brackets_diagram() {
    if (!isset($_POST['bracket_id'])) {
        wp_send_json_error(array('message' => 'Bracket ID is required!'));
    }
    
    // Your PHP logic here
    $bracket_id = $_POST['bracket_id'];
    wp_send_json_success(['message' => 'Bracket recuperado correctamente', 'html' => on_fetch_bracket_data_fe($bracket_id)['html'], 'elements' => on_fetch_bracket_data_fe($bracket_id)['elements']]);
}

function fetch_tournament_brackets_display() {
    if (!isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Tournament ID is required!'));
    }
    
    // Your PHP logic here
    $tournament_id = $_POST['tournament_id'];
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    $html = render_brackets_fe_response($tournament);

    wp_send_json_success(array('message' => 'Teams fetched successfully!', 'html' => $html));
}

add_action('wp_ajax_fetch_brackets_diagram', 'fetch_brackets_diagram');
add_action('wp_ajax_nopriv_fetch_brackets_diagram', 'fetch_brackets_diagram');
add_action('wp_ajax_fetch_tournament_brackets_display', 'fetch_tournament_brackets_display');
add_action('wp_ajax_nopriv_fetch_tournament_brackets_display', 'fetch_tournament_brackets_display');
