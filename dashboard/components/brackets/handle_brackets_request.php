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

  $official_name = OfficialsDatabase::get_official_by_id($match->official_id)->official_name;

  $match_time = $match->match_time . ":00";

  $html = "<div id='match_" . $match->match_id . "' class='bracket-match'>";
  $html .= "<span>" . $team_1_name . "</span>";
  $html .= "<span>VS</span>";
  $html .= "<span>" . $team_2_name . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data-container'>";
  $html .= "<div class='match-data'>";
  $html .= "<span>Fecha: " . $match->match_date . "</span>";
  $html .= "<span>Hora: " . $match_time . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data'>";
  $html .= "<span>Arbitro: " . $official_name . "</span>";
  $html .= "<span>Campo: " . $match->field_number . "</span>";
  $html .= "</div>";
  $html .= "</div>";
  return $html;
}

function on_fetch_bracket_data(int $bracket_id) {
  $matches = PendingMatchesDatabase::get_matches_by_bracket($bracket_id);

  $bracket_rounds = array_unique(array_map(function($match) {
    return $match->bracket_round;
  }, $matches));

  $html = "<div class='bracket-container'>";
  foreach ($bracket_rounds as $round) {
    $html .= "<div id='round_" . $round . "' class='bracket-round'>";

    foreach ($matches as $match) {
      if ($match->bracket_round == $round) {
        $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
        $html .= create_bracket_match($match);
        $html .= "</div>";
      }
    }

    $html .= "</div>";
  }

  $html .= "</div>";
  return $html;
}

function fetch_bracket_data() {
  if (!isset($_POST['bracket_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $bracket_id = intval($_POST['bracket_id']);

  wp_send_json_success(['message' => 'Division agregada correctamente', 'html' => on_fetch_bracket_data($bracket_id)]);
}

add_action('wp_ajax_fetch_bracket_data', 'fetch_bracket_data');
add_action('wp_ajax_nopriv_fetch_bracket_data', 'fetch_bracket_data');
