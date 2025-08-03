<?php

function render_standings($team_id, $tournament_id) {
	$html = "";
	$html .= "<h2 style='text-align: center;'>Resultados del equipo</h2>";

	$pending_matches = PendingMatchesDatabase::get_pending_matches_by_team($team_id, $tournament_id);
	$played_matches = MatchesDatabase::get_matches_by_team($team_id, $tournament_id);
	$bracket_id = TeamsDatabase::get_team_by_id($team_id)->division_id;
	$bracket = BracketsDatabase::get_bracket_by_division($bracket_id, $tournament_id);
	$team = TeamsDatabase::get_team_by_id($team_id);
	$division_name = DivisionsDatabase::get_division_by_id($team->division_id)->division_name;

	$html .= "<div class='team-results-container'>";
	$html .= "<div class='team-results'>";
	$html .= "<div class='team-item-results'>";
	$html .= "<img class='team-logo' src='" . wp_get_attachment_image_url($team->logo, 'full') . "' alt='" . $team->team_name . "' />";
	$html .= "<span style='font-weight: bold; text-align: center;'>" . esc_html($team->team_name) . "</span>";
	$html .= "</div>";
	$html .= "<div>";
	$html .= "<span>Partidos Jugados: <span style='font-weight: bold;'>" . count($played_matches) . "</span></span>";
	$html .= "<span>Partidos Pendientes: <span style='font-weight: bold;'>" . count($pending_matches) . "</span></span>";
	$html .= "</div>";
	$html .= "</div>";

	$html .= "<div class='matches-results-container'>";
	$html .= "<span style='text-align: center; font-size: 24px; font-weight: bold;'>Resultados</span>";

	if (empty($played_matches)) {
		$html .= "<h3 style='text-align: center;'>Este equipo aun no ha jugado partidos</h3>";
	}
	
	$html .= "<hr class='match-separator' />";

	foreach ($played_matches as $match) {
		$team_1 = TeamsDatabase::get_team_by_id($match->team_id_1);
		$team_2 = TeamsDatabase::get_team_by_id($match->team_id_2);
		$team_1_name = $team_1->team_name;
		$team_2_name = $team_2->team_name;
		$team_1_logo = $team_1->logo;
		$team_2_logo = $team_2->logo;

		$division_name = DivisionsDatabase::get_division_by_id($match->division_id)->division_name;
	
		$official = OfficialsDatabase::get_official_by_id($match->official_id);
		$official_name = $official ? $official->official_name : "No Asignado";

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
		$html .= "<div class='match-info-left'>";
		$html .= "<span>Fecha: " . $match->match_date . "</span>";
		$html .= "<span>Hora: " . $match_time . "</span>";
		$html .= "<span>Division: " . $division_name . "</span>";
		$html .= "</div>";
	
		$html .= "<div class='match-results'>";
		$html .= "<div class='match-team-container left-team $match_winner_class_1'>
								<div class='white-space'></div>						
								<div class='team-name'>
									<img width='50' height='50' src='" . wp_get_attachment_image_url($team_1_logo, 'full') . "' alt='" . $team_1_name . "' />
									<span>" . $team_1_name . "</span>
								</div>
							</div>";
		$html .= "<div class='match-scoreboard'>
								<span>" . $match->goals_team_1 . "</span>
								<span>-</span>
								<span>" . $match->goals_team_2 . "</span>
							</div>";
		$html .= "<div class='match-team-container right-team $match_winner_class_2'>
								<div class='team-name'>
									<span>" . $team_2_name . "</span>
									<img width='50' height='50' src='" . wp_get_attachment_image_url($team_2_logo, 'full') . "' alt='" . $team_2_name . "' />
								</div>
								<div class='white-space'></div>
							</div>";
		$html .= "</div>";
		
		$html .= "<div class='match-info-right'>";
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
		
		$html .= "<hr class='match-separator' />";
	}

	$html .= "<div class='bracket-results'>";
	$html .= render_leaderboard_table($bracket->bracket_id);
	$html .= "<hr/>";
	$html .= "</div>";
	$html .= "</div>";
	$html .= "</div>";
	return $html;
}

function fetch_team_standings() {
  if (!isset($_POST['team_id']) || !isset($_POST['tournament_id'])) {
    wp_send_json_error(array('message' => 'Team ID is required!'));
  }

  $team_id = $_POST['team_id'];
  $tournament_id = $_POST['tournament_id'];
  $html = render_standings($team_id, $tournament_id);

  wp_send_json_success(array('message' => 'Standings fetched successfully!', 'standings' => $html));
}

function fetch_division_team_standings() {
  if (!isset($_POST['division_id'])) {
    wp_send_json_error(array('message' => 'Division ID is required!'));
  }

  $division_id = $_POST['division_id'];
  $teams = TeamsDatabase::get_enrolled_teams_by_division($division_id);
  $html = "";
  if (empty($teams)) {
    $html = "<option value=''>No hay equipos inscritos en esta division</option>";
    wp_send_json_success(array('message' => 'Teams fetched successfully!', 'teams' => $html));
  }
  $html = "<option value=''>Selecciona un equipo</option>";
  foreach ($teams as $team) {
    $html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
  }

  wp_send_json_success(array('message' => 'Teams fetched successfully!', 'teams' => $html));
}

function fetch_tournament_divisions_standings() {
    if (!isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Tournament ID is required!'));
    }
    
    // Your PHP logic here
    $tournament_id = $_POST['tournament_id'];
    $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
    $divisions_html = "";
    $teams_html = "";

    if (empty($divisions)) {
      $divisions_html .= "<option value=''>No hay divisiones registradas en este torneo</option>";
      $teams_html .= "<option value=''>No hay equipos inscritos en este torneo</option>";
      wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'divisions' => $divisions_html, 'teams' => $teams_html));
    } 
      
    $divisions_html .= "<option value=''>Selecciona una division</option>";
    foreach ($divisions as $division) {
        $divisions_html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . "</option>";
    }


    $teams = TeamsDatabase::get_enrolled_teams_by_division($divisions[0]->division_id);
    if (empty($teams)) {
      $teams_html .= "<option value=''>No hay equipos inscritos en esta division</option>";
    } else {
      $teams_html .= "<option value=''>Selecciona un equipo</option>";
    }

    foreach ($teams as $team) {
        $teams_html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
    }

    wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'divisions' => $divisions_html, 'teams' => $teams_html));
}

add_action('wp_ajax_fetch_team_standings', 'fetch_team_standings');
add_action('wp_ajax_nopriv_fetch_team_standings', 'fetch_team_standings');
add_action('wp_ajax_fetch_division_team_standings', 'fetch_division_team_standings');
add_action('wp_ajax_nopriv_fetch_division_team_standings', 'fetch_division_team_standings');
add_action('wp_ajax_fetch_tournament_divisions_standings', 'fetch_tournament_divisions_standings');
add_action('wp_ajax_nopriv_fetch_tournament_divisions_standings', 'fetch_tournament_divisions_standings');
