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

  $hardcoded_match_positions =[
    2 => [2, 1],
    4 => [4,2,3,1],
    8 => [8,4,6,2,7,3,5,1],
    16 => [16,8,12,4,14,6,10,2,15,7,11,3,13,59,1],
    32 => [32,16,24,8,28,12,20,4,31,15,23,7,27,13,19,5,30,14,22,6,26,10,25,3,29,9,11,1,33,17,21,18],
  ];
  $bracket_rounds = array_unique(array_map(function($match) {
    return $match->bracket_round;
  }, $matches));

  $html = "<div class='bracket-container'>";
  $elements = [];
  $total_rounds = count($bracket_rounds);
  foreach ($bracket_rounds as $round) {
    $html .= "<div id='round_" . $round . "' class='bracket-round'>";

    $elements[$round] = [];
    if ($round != 0) {
      foreach ($matches as $match) {
        if ($match->bracket_round == $round) {
          $elements[$round][] = "match_" . $match->match_id;
          $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
          $html .= create_bracket_match($match);
          $html .= "</div>";
        }
      }
    } else {
      $matches_this_round = [];
      $maximum_matches_this_round = pow(2, $total_rounds - $round - 1);

      $temp_matches = [];

      foreach ($matches as $match) {
        if ($match->bracket_round == $round) {
          $matches_this_round[] = $match;
        }
      }

      if ($maximum_matches_this_round == count($matches_this_round)) {
        foreach ($matches_this_round as $match) {
          $elements[$round][] = "match_" . $match->match_id;
          $html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
          $html .= create_bracket_match($match);
          $html .= "</div>";
        }
      } else {
        for ($i = 0; $i < $maximum_matches_this_round-1; $i++) {
          $elements[$round][] = null;
          $temp_matches[] = "<div id='match_null' class='bracket-match-container-empty'></div>";
        }

        foreach ($matches_this_round as $index => $match) {
          $elements[$round][$hardcoded_match_positions[$maximum_matches_this_round][$index]-1] = "match_" . $match->match_id;
          $temp_matches[$hardcoded_match_positions[$maximum_matches_this_round][$index]-1] =  "<div id='match_" . $match->match_id . "' class='bracket-match-container'>" . create_bracket_match($match) . "</div>";
        }

        $html .= implode("", $temp_matches);
      }
    }
    $html .= "</div>";
  }

  $html .= "</div>";
  return ['html' => $html, 'elements' => $elements];
}

function fetch_bracket_data() {
  if (!isset($_POST['bracket_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $bracket_id = intval($_POST['bracket_id']);

  wp_send_json_success(['message' => 'Bracket recuperado correctamente', 'html' => on_fetch_bracket_data($bracket_id)['html'], 'elements' => on_fetch_bracket_data($bracket_id)['elements']]);
}

add_action('wp_ajax_fetch_bracket_data', 'fetch_bracket_data');
add_action('wp_ajax_nopriv_fetch_bracket_data', 'fetch_bracket_data');
