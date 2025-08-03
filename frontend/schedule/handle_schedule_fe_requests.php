<?php

function render_match_details($match) {
    $team_1_name = "TBD";
    $team_2_name = "TBD";
  
    if ($match->team_id_1) {
      $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
    }
  
    if ($match->team_id_2) {
      $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
    }
  
    $official_name = "Por Asignar";
    if ($match->official_id) {
      $official_name = OfficialsDatabase::get_official_by_id($match->official_id)->official_name;
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
    $html .= "<span>Arbitro: " . $official_name . "</span>";
    $html .= "<span>Campo: " . $match->field_number . "</span>";
    $html .= "</div>";
    $html .= "</div>";
    return $html;
}

function render_team_schedule_table($matches, $tournament_id) {
    $tournament_days = TournamentsDatabase::get_tournament_by_id($tournament_id)->tournament_days;
    $tournament_days = explode(",", $tournament_days);

    $html = "";

    $html .= "<table border='1' id='team-schedule-table'>";
    $html .= "<caption style='text-align: center;padding: 10px;font-size: 28px;'>Horarios</caption>";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>Hora</th>";

    foreach ($tournament_days as $day) {
        $html .= "<th>" . esc_html($day) . "</th>";
    }

    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";

    // get earliest match time
    $earliest_match_time = $matches[0]->match_time;
    foreach ($matches as $match) {
        if ($match->match_time < $earliest_match_time) {
            $earliest_match_time = $match->match_time;
        }
    }

    // get latest match time
    $latest_match_time = $matches[0]->match_time;
    foreach ($matches as $match) {
        if ($match->match_time > $latest_match_time) {
            $latest_match_time = $match->match_time;
        }
    }

    for ($i = $earliest_match_time - 1; $i <= $latest_match_time + 1; $i++) {
        $html .= "<tr>";
        $html .= "<td>" . $i . "</td>";
        foreach ($tournament_days as $day) {
          $has_match = false;
          foreach ($matches as $match) {
            if ($i == $match->match_time && $match->match_date == $day) {
              $html .= "<td class='match'>X</td>";
              $has_match = true;
            }
          }
          if (!$has_match) $html .= "<td></td>";
        }
        $html .= "</tr>";
    }

    $html .= "</tbody>";
    $html .= "</table>";

    $html .= "<div class='team-matches-container'>";
    $html .= "<span style='font-size: 28px;'>Partidos</span>";
    $html .= "<div class='team-matches'>";
    
    foreach ($matches as $match) {
      $html .= "<div class='bracket-match-container'>";
      $html .= render_match_details($match);
      $html .= "</div>";
    }
    $html .= "</div>";
    return $html;
}

function fetch_team_schedule_fe() {
  if (!isset($_POST['team_id']) || !isset($_POST['tournament_id'])) {
    wp_send_json_error(array('message' => 'Team ID is required!'));
  }

  $team_id = $_POST['team_id'];
  $tournament_id = $_POST['tournament_id'];
  $matches = PendingMatchesDatabase::get_all_matches_by_team($team_id, $tournament_id);
  $html = render_team_schedule_table($matches, $tournament_id);

  wp_send_json_success(array('message' => 'Schedules fetched successfully!', 'matches' => $html));
}

function fetch_division_teams_fe() {
  if (!isset($_POST['division_id'])) {
    wp_send_json_error(array('message' => 'Division ID is required!'));
  }

  $division_id = $_POST['division_id'];
  $teams = TeamsDatabase::get_enrolled_teams_by_division($division_id);
  $html = "";
  foreach ($teams as $team) {
    $html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
  }

  $tournament_id = $_POST['tournament_id'];
  $matches = PendingMatchesDatabase::get_all_matches_by_team($teams[0]->team_id, $tournament_id);
  $matches_html = render_team_schedule_table($matches, $tournament_id);

  wp_send_json_success(array('message' => 'Teams fetched successfully!', 'teams' => $html, 'matches' => $matches_html));
}

function fetch_tournament_divisions_fe() {
    if (!isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Tournament ID is required!'));
    }
    
    // Your PHP logic here
    $tournament_id = $_POST['tournament_id'];
    $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
    $divisions_html = "";
    foreach ($divisions as $division) {
        $divisions_html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . "</option>";
    }

    $teams = TeamsDatabase::get_enrolled_teams_by_division($divisions[0]->division_id);
    $teams_html = "";
    foreach ($teams as $team) {
        $teams_html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
    }

    $matches = PendingMatchesDatabase::get_all_matches_by_team($teams[0]->team_id, $tournament_id);
    $matches_html = render_team_schedule_table($matches, $tournament_id);

    wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'divisions' => $divisions_html, 'teams' => $teams_html, 'matches' => $matches_html));
}

add_action('wp_ajax_fetch_team_schedule_fe', 'fetch_team_schedule_fe');
add_action('wp_ajax_nopriv_fetch_team_schedule_fe', 'fetch_team_schedule_fe');
add_action('wp_ajax_fetch_tournament_divisions_fe', 'fetch_tournament_divisions_fe');
add_action('wp_ajax_nopriv_fetch_tournament_divisions_fe', 'fetch_tournament_divisions_fe');
add_action('wp_ajax_fetch_division_teams_fe', 'fetch_division_teams_fe');
add_action('wp_ajax_nopriv_fetch_division_teams_fe', 'fetch_division_teams_fe');
