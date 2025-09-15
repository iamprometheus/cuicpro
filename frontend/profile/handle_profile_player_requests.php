<?php 
// only players
function render_user_team() {
	$user_id = get_current_user_id();
	$player_user = PlayersUserDatabase::get_player_by_id($user_id);
	if (!$player_user->user_has_team) {
		return render_player_join_team();
	}

	$player_instances = PlayersDatabase::get_player_by_user_id($user_id);
	$player = $player_instances[count($player_instances) - 1];

	$team = TeamsDatabase::get_team_by_id($player->team_id);
	
	$team_id = $team->team_id;

	$html = "<div class='info-header'>
						<h2>" . $team->team_name . "</h2>
					</div>";
	$html .= "<div class='team-info'>";
	$html .= "<div>";
	$html .= "<span id='team-id' data-team-id='" . esc_attr($team_id) . "'>ID: " . esc_html($team_id) . "</span>";
	$html .= "</div>";
	$html .= "<div class='team-logo-container'>";
	$html .= "<span>Logo:</span>";
	$html .= "<img src='" . wp_get_attachment_image_url($team->logo, 'full') . "' alt='" . $team->team_name . "' />";
	$html .= "</div>";
	$html .= "<div>";
	$html .= "<span>Torneo: " . esc_html(TournamentsDatabase::get_tournament_by_id($team->tournament_id)->tournament_name) . "</span>";
	$html .= "</div>";
	$html .= "<div>";
	$html .= "<span>Division: " . esc_html(DivisionsDatabase::get_division_by_id($team->division_id)->division_name) . "</span>";
	$html .= "</div>";
	$html .= "<div>";
	$html .= "<span>Coach: " . esc_html(CoachesDatabase::get_coach_by_id($team->coach_id)->coach_name) . "</span>";
	$html .= "</div>";

	$players = PlayersDatabase::get_players_by_team($team_id);

	$html .= "<div class='team-players'>";
	$html .= "<h2 style='text-align: center;'>Jugadores</h2>";
	$html .= "<div class='user-players'>";
	foreach ($players as $player) {
		$html .= "<div data-player-id='" . esc_attr($player->player_id) . "' class='player-item'>";
		//$html .= "<img class='player-photo' src='" . wp_get_attachment_image_url($player->player_photo, 'full') . "' alt='" . $player->player_name . "' />";
		$html .= "<span id='player-" . esc_attr($player->player_id) . "'>" . esc_html($player->player_name) . "</span>";
		$html .= "</div>";
	}
	$html .= "</div>";
	$html .= "</div>";

	$html .= "<hr style='border: 2px solid black; width: 100%;'/>";
	$html .= "<h2 style='text-align: center;'>Zona de Peligro</h2>";
	$html .= "<div class='action-buttons-container'>";
	$html .= "<button class='danger-button' id='show-leave-team-dialog' type='button' data-team-id='" . esc_attr($team->team_id) . "'>Salir del Equipo</button>";
	$html .= "<dialog id='leave-team-dialog'>";
	$html .= "<p>¿Estás seguro de salir de este equipo?</p>";
	$html .= "<div class='action-buttons-container'>";
	$html .= "<button id='cancel-leave-team-button' type='button'>Volver</button>";
	$html .= "<button id='confirm-leave-team-button' type='button' class='danger-button' data-team-id='" . esc_attr($team->team_id) . "'>Salir del Equipo</button>";
	$html .= "</div>";
	$html .= "</dialog>";
	$html .= "</div>";

	$html .= "</div>";
	return $html;
}

function render_player_join_team() {
    $user = wp_get_current_user();
    $player_instance = PlayersUserDatabase::get_player_by_id($user->ID);
	$tournaments = TournamentsDatabase::get_active_tournaments();

    // $src = '#';
    // $required = "required";
    // if ($player_instance) {
    //     $src = wp_get_attachment_image_url($player_instance->user_photo, 'full');
    //     $required = "";
    // }

	$html = "<div class='info-header'>
						<h2>Unirse a un equipo</h2>
					</div>";
	$html .= "<form id='join-team-form' class='create-team-form'>";

	// $html .= "				<div class='create-team-form-group'>
	// 							<label for='logo'>Mi Foto:</label>
	// 							<div class='logo-container'>
	// 								<input type='file' id='logo' name='logo' $required/>
	// 								<div class='logo-preview'>
	// 									<img id='logo-preview' src='$src' width='100' height='100' alt='Foto' />
	// 								</div>
	// 							</div>
	// 						</div>";

	$html .= "<div class='create-team-form-group'>
				<label for='tournament_id'>Torneo:</label>
				<select name='tournament_id' id='tournament_dropdown_id' class='form-input' required>
					<option value=''>Selecciona el torneo</option>";
					foreach ($tournaments as $tournament) {
						$html .= "<option value='" . esc_attr($tournament->tournament_id) . "'>" . esc_html($tournament->tournament_name) . "</option>";
					}
	$html .= "  </select>
			</div>";
	$html .= "<div class='create-team-form-group'>
				<label for='division_id'>Categoria:</label>
				<select name='division_id' id='division_dropdown_id' class='form-input' required>
					<option value=''>Selecciona la categoria</option>
				</select>
			  </div>";

	$html .= "<div class='create-team-form-group'>
				<label for='team_id'>Equipo:</label>
				<select name='team_id' id='team_dropdown_id' class='form-input' required>
					<option value=''>Selecciona el equipo</option>
				</select>
				</div>
			  <button type='submit'>Unirse al equipo</button>
			</form>";
	return $html;
}

function fetch_teams_by_coach() {
	if (!isset($_POST['coach_id'])) {
		wp_send_json_error(array('message' => 'Coach ID no encontrado'));
		return;
	}
	$coach_id = sanitize_text_field($_POST['coach_id']);
	$teams = TeamsUserDatabase::get_teams_by_coach($coach_id);

	$html = "";
	foreach ($teams as $team) {
		$html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
	}
    if (empty($teams)) {
        $html .= "<option value=''>No hay equipos disponibles</option>";
    }

	wp_send_json_success(array('html' => $html));
}

function fetch_teams_by_divisions_profile() {
	if (!isset($_POST['division_id'])) {
		wp_send_json_error(array('message' => 'Division ID no encontrado'));
		return;
	}
	$division_id = sanitize_text_field($_POST['division_id']);
	$teams = TeamsDatabase::get_teams_by_division($division_id);

	$html = "";
	
    if (empty($teams)) {
		$html .= "<option value=''>No hay equipos disponibles</option>";
    } else{
		$html .= "<option value=''>Selecciona el equipo</option>";
	}
	foreach ($teams as $team) {
		$html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
	}

	wp_send_json_success(array('html' => $html));
}

function fetch_divisions_by_tournament_profile() {
	if (!isset($_POST['tournament_id'])) {
		wp_send_json_error(array('message' => 'Tournament ID no encontrado'));
		return;
	}
	$tournament_id = sanitize_text_field($_POST['tournament_id']);
	$divisions = DivisionsDatabase::get_divisions_by_tournament($tournament_id);

	$html = "";
	if (empty($divisions)) {
		$html .= "<option value=''>No hay divisiones disponibles</option>";
	} else {
		$html .= "<option value=''>Selecciona la division</option>";
	}
	foreach ($divisions as $division) {
		$html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . "</option>";
	}

	wp_send_json_success(array('html' => $html));
}

function render_player_results() {
	$html = "<h2 style='text-align: center;'>Mis Torneos</h2>";
    $html .= "<div class='user-tournaments'>";

    $user = wp_get_current_user();
    $player_instances = PlayersDatabase::get_player_by_user_id($user->ID);
    if (empty($player_instances)) {
        $html .= "<h3>Registrate en un equipo para poder ver resultados</h3>";
        return $html;
    }

    $player_instances = array_reverse($player_instances);
    $tournaments = [];
	$teams = [];
    foreach ($player_instances as $player_instance) {
		$team = TeamsDatabase::get_team_by_id($player_instance->team_id);
        $tournament_id = $team->tournament_id;
        if (in_array($tournament_id, $tournaments)) {
            continue;
        }
        $tournaments[] = $tournament_id;
		$teams[$tournament_id] = $player_instance->team_id;
    }

    foreach ($tournaments as $tournament_id) {
        $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
        $html .= "<div id='tournament-played-player' data-team-id='" . esc_attr($teams[$tournament_id]) . "' data-tournament-id='" . esc_attr($tournament_id) . "' class='tournament-item-fe'>";
        $html .= "<span>" . esc_html($tournament->tournament_name) . "</span>";
        $html .= "</div>";
    }
    $html .= "</div>";
    return $html;
}

function handle_results_for_player_team() {
    if (!isset($_POST['team_id']) || !isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Team ID o Tournament ID no encontrado'));
        return;
    }
    $team_id = sanitize_text_field($_POST['team_id']);
    $tournament_id = sanitize_text_field($_POST['tournament_id']);
    $results = render_player_team_and_division_results($team_id, $tournament_id);
    wp_send_json_success(array('html' => $results['html'], 'elements' => $results['elements']));
}

function render_player_team_and_division_results($team_id, $tournament_id) {
	$html = "";
	$html .= "<div class='info-header'>
                    <span id='back-button' data-tournament-id='" . esc_attr($tournament_id) . "' data-screen='player-results'>Volver</span>
                    <h2>Resultados del equipo</h2>
                </div>";

    $team = TeamsDatabase::get_team_by_id($team_id);

	$pending_matches = PendingMatchesDatabase::get_pending_matches_by_team($team_id, $tournament_id);
	$played_matches = MatchesDatabase::get_matches_by_team($team_id, $tournament_id);
	$bracket_id = $team->division_id;
	$bracket = BracketsDatabase::get_bracket_by_division($bracket_id, $tournament_id);
	$division_name = DivisionsDatabase::get_division_by_id($team->division_id)->division_name;

	$html .= "<div class='team-results-container'>";
	$html .= "<div class='team-results'>";
	$html .= "<div class='team-item-results'>";
	$html .= "<img class='team-logo' src='" . wp_get_attachment_image_url($team->logo, 'full') . "' alt='" . $team->team_name . "' />";
	$html .= "<span>" . esc_html($team->team_name) . "</span>";
	$html .= "<span style='font-size: 12px;'>" . esc_html($division_name) . "</span>";
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

	$html .= "<span style='font-weight: bold; font-size: 24px; text-align: center;'>Resultados del bracket</span>";
	$html .= "<div class='bracket-results'>";

	$elements = [];
	if ($bracket) {
		$html .= render_leaderboard_table($bracket->bracket_id);
		$html .= "<hr/>";
		$playoffs = render_playoffs_results($bracket->bracket_id);
		$html .= $playoffs['html'];
		$elements = $playoffs['elements'];
	} else {
		$html .= "<span style='text-align: center;'>Aun resultados que mostrar</span>";
	}

	$html .= "</div>";
	$html .= "</div>";
	$html .= "</div>";
	return ['html' => $html, 'elements' => $elements];
}

// function render_player_team_results() {
//     $team_id = sanitize_text_field($team_id);
//     $results = TeamsDatabase::get_team_results($team_id);
//     wp_send_json_success(array('results' => $results));
// }

function handle_leave_team() {
	$user_id = wp_get_current_user()->ID;
	PlayersUserDatabase::update_player_has_team($user_id, false);
    PlayersDatabase::delete_player_by_user_id($user_id);

	wp_send_json_success(array('message' => 'Equipo abandonado exitosamente', 'html' => render_player_join_team()));
}

function handle_join_team_form() {
	$team_id = sanitize_text_field($_POST['team_id']);

	$user_id = get_current_user_id();
	$player_name = PlayersUserDatabase::get_player_by_id($user_id)->user_name;
	$team = TeamsDatabase::get_team_by_id($team_id);
	$coach = CoachesDatabase::get_coach_by_id($team->coach_id);
    // wp_send_json_error(array('message' => $user_id, 'coach' => $coach->coach_id, 'player_name' => $player_name, 'team_id' => $team_id, 'coach_user_id' => $coach->coach_user_id));
	
	$result = PlayersDatabase::insert_player(
		$user_id, 
		$player_name, 
		$team_id, 
		"", 
		$coach->coach_id, 
		$coach->coach_user_id);

	if (!$result[0]) wp_send_json_error(
		array('message' => $result, 
		'user_id' => $user_id, 
		'coach' => $coach->coach_id, 
		'player_name' => $player_name, 
		'team_id' => $team_id, 
		'coach_user_id' => $coach->coach_user_id));
		
	PlayersUserDatabase::update_player_has_team($user_id, true);

    wp_send_json_success(array('html' => render_user_team()));
}

add_action('wp_ajax_nopriv_fetch_divisions_by_tournament_profile', 'fetch_divisions_by_tournament_profile');
add_action('wp_ajax_fetch_divisions_by_tournament_profile', 'fetch_divisions_by_tournament_profile');
add_action('wp_ajax_nopriv_fetch_teams_by_divisions_profile', 'fetch_teams_by_divisions_profile');
add_action('wp_ajax_fetch_teams_by_divisions_profile', 'fetch_teams_by_divisions_profile');
add_action('wp_ajax_nopriv_render_player_results', 'render_player_results');
add_action('wp_ajax_render_player_results', 'render_player_results');
add_action('wp_ajax_nopriv_handle_results_for_player_team', 'handle_results_for_player_team');
add_action('wp_ajax_handle_results_for_player_team', 'handle_results_for_player_team');
add_action('wp_ajax_nopriv_handle_leave_team', 'handle_leave_team');
add_action('wp_ajax_handle_leave_team', 'handle_leave_team');
add_action('wp_ajax_handle_join_team_form', 'handle_join_team_form');
add_action('wp_ajax_nopriv_handle_join_team_form', 'handle_join_team_form'); // for non-logged-in users