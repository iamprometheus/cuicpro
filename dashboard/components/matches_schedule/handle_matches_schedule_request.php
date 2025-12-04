<?php

if (!function_exists('switch_selected_tournament_matches_schedule')) {
  function switch_selected_tournament_matches_schedule()
  {
    if (!isset($_POST['tournament_id'])) {
      wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
    }
    $tournament_id = intval($_POST['tournament_id']);
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    if (!$tournament) {
      wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
    }
    wp_send_json_success([
      'message' => 'Torneo seleccionado correctamente',
      'schedule' => create_table_for_matches_schedule($tournament)
    ]);
  }
}

function check_overlapping_matches($new_matches)
{
  foreach ($new_matches as $match_id => $new_data) {
    $match = PendingMatchesDatabase::get_match_by_id($match_id);
    $tournament_days = TournamentsDatabase::get_tournament_by_id($match->tournament_id)->tournament_days;
    $days = explode(",", $tournament_days);
    $team1_matches_results = [];
    $team2_matches_results = [];

    if ($match->team_id_1) {
      $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
      $team1_matches = PendingMatchesDatabase::get_matches_by_team($match->team_id_1, $match->tournament_id);

      $team1_matches_results = array_filter($team1_matches, function ($match) use ($new_data, $days) {
        return $match->match_date == $days[$new_data['match_date']] && $match->match_time == $new_data['match_time'];
      });
    }

    if ($match->team_id_2) {
      $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
      $team2_matches = PendingMatchesDatabase::get_matches_by_team($match->team_id_2, $match->tournament_id);

      $team2_matches_results = array_filter($team2_matches, function ($match) use ($new_data, $days) {
        return $match->match_date == $days[$new_data['match_date']] && $match->match_time == $new_data['match_time'];
      });
    }

    if (count($team1_matches_results) > 0) {
      return [false, $team_1_name];
    }

    if (count($team2_matches_results) > 0) {
      return [false, $team_2_name];
    }
  }

  return [true, ""];
}

function update_matches_schedule()
{
  $modified_matches = $_POST['modified_matches'];
  $tournament_id = intval($_POST['tournament_id']);

  // check if all updated matches are possible (e.g. no team have two matches at the same time)
  $overlapping_results = check_overlapping_matches($modified_matches);
  if (!$overlapping_results[0]) {
    wp_send_json_error([
      'message' => 'No se pudo actualizar el horario.',
    ]);
  }

  foreach ($modified_matches as $match_id => $new_data) {
    $tournament_days = TournamentsDatabase::get_tournament_by_id($tournament_id)->tournament_days;
    $days = explode(",", $tournament_days);

    // check if match has official assigned
    $match = PendingMatchesDatabase::get_match_by_id($match_id);
    if ($match->official_id) {
      // get official and new official hours for the day of the match
      $official_hours = OfficialsHoursDatabase::get_official_hours_by_day($match->official_id, $match->match_date);

      // add match hour to official available hours
      $official_new_hours = explode(",", $official_hours->official_available_hours);
      $official_new_hours[] = $match->match_time;
      $official_new_hours = implode(",", $official_new_hours);
      OfficialsHoursDatabase::update_official_available_hours($official_hours->official_hours_id, $official_new_hours);

      // de-assing official from match
      PendingMatchesDatabase::remove_official($match_id);
    }

    // update match data
    PendingMatchesDatabase::update_match_date_time_and_field(
      $match_id,
      $days[intval($new_data['match_date'])],
      intval($new_data['match_time']),
      intval($new_data['match_field']),
      intval($new_data['match_field_type'])
    );
  }

  return wp_send_json_success([
    'message' => 'Partidos modificados correctamente',
  ]);
}

add_action('wp_ajax_update_matches_schedule', 'update_matches_schedule');
add_action('wp_ajax_nopriv_update_matches_schedule', 'update_matches_schedule');
add_action('wp_ajax_switch_selected_tournament_matches_schedule', 'switch_selected_tournament_matches_schedule');
add_action('wp_ajax_nopriv_switch_selected_tournament_matches_schedule', 'switch_selected_tournament_matches_schedule');
