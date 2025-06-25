<?php

function cuicpro_matches($tournament) {
  $html = "";
  if (is_null($tournament)) {
    return "<span>No hay partidos para mostrar</span>";
  }

  $matches = MatchesDatabase::get_matches_by_tournament($tournament->tournament_id);

  if (empty($matches)) {
    return "<span>No hay partidos para mostrar para el torneo seleccionado</span>";
  }

  $days = array_unique(array_map(function($match) {
    return $match->match_date;
  }, $matches));

  
	$html .= "<div style='margin-bottom: 15px; font-size: 20px;'>
              <span style='font-weight: bold; '>Partidos registrados en torneo seleccionado</span>
            </div>";

  foreach ($days as $day) {
    $html .= "<hr class='day-separator' />";
    $html .= "<div class='day-results-title'> Partidos del dia: " . $day . "</div>";
    $html .= "<div id='day_" . $day . "' class='day-results-container'>";

    $matches_count = count(array_filter($matches, function($match) use ($day) {
      return $match->match_date == $day;
    }));

    foreach ($matches as $match) {
      if ($match->match_date != $day) continue;

      $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
      $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
    
      $official = OfficialsDatabase::get_official_by_id($match->official_id);
      $official_name = $official ? $official->official_name : "Asignacion Pendiente";

      $match_winner = "";
      if ($match->match_winner) {
        $match_winner = TeamsDatabase::get_team_by_id($match->match_winner)->team_name;
      } else {
        $match_winner = "Empate";
      }
    
      $match_time = $match->match_time . ":00";
      $match_winner_class_1 = $match_winner == $team_1_name ? 'winner' : '';
      $match_winner_class_2 = $match_winner == $team_2_name ? 'winner' : '';
      
      $html .= "<div class='match-item'>";
      $html .= "<div class='match-hour'>";
      $html .= "<span>" . $match_time . "</span>";
      $html .= "</div>";
    
      $html .= "<div class='match-results'>";
      $html .= "<div class='match-team-name $match_winner_class_1'>
                  <span>" . $team_1_name . "</span>
                </div>";
      $html .= "<div class='match-scoreboard'>
                  <span>" . $match->goals_team_1 . "</span>
                  <span>-</span>
                  <span>" . $match->goals_team_2 . "</span>
                </div>";
      $html .= "<div class='match-team-name $match_winner_class_2'>
                  <span>" . $team_2_name . "</span>
                </div>";
      $html .= "</div>";
      
      $html .= "<div class='match-official-field'>";
      $html .= "<div>";
      $html .= "<span>Arbitro: </span>";
      $html .= "<span>" . $official_name . "</span>";
      $html .= "</div>";
      $html .= "<div>";
      $html .= "<span>Campo: </span>";
      $html .= "<span>" . $match->field_number . "</span>";
      $html .= "</div>";
      $html .= "</div>";

      $html .= "</div>";

      $matches_count--;
      if ($matches_count > 0) {
        $html .= "<hr class='match-separator' />";
      }
    }
    $html .= "</div>";
  }

  return $html;
}

function cuicpro_matches_viewer() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }

  // create table header
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
  $html .= "<div class='table-view-container'>";
  $html .= "<div id='matches-data' style='width: 100%;'>";
  
  $html .= cuicpro_matches($tournament);
  $html .= "</div>";
  $html .= "</div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_matches_scripts() {
	wp_enqueue_script(
			'matches-script',
			plugins_url('/handle_matches_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('matches-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_matches_scripts');

require_once __DIR__ . '/handle_matches_request.php';