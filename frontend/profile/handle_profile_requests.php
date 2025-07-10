<?php

if (!function_exists('render_user_data')) {
	function render_user_data() {
		$user_id = get_current_user_id();
		$coach = CoachesUserDatabase::get_coach_by_id($user_id);

		$html = "<h2 style='text-align: center;'>Mis Datos</h2>";
		$html .= "<form id='user-data-form' class='user-data-form'>
							<div class='form-group'>
								<label for='name'>ID:</label>
								<input name='name' class='form-input' type='text' value='" . $coach->coach_id . "' disabled/>
							</div>
							<div class='form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' value='" . $coach->coach_name . "' required/>
							</div>
							<div class='form-group'>
								<label for='phone'>Telefono:</label>
								<input name='phone' class='form-input' type='text' value='" . $coach->coach_contact . "' required/>
							</div>
							<div class='form-group'>
								<label for='location'>Ubicacion:</label>
								<div class='multiple-fields-container'>
									<input name='city' class='form-input' type='text' value='" . $coach->coach_city . "' required/>
									<input name='state' class='form-input' type='text' value='" . $coach->coach_state . "' required/>
									<input name='country' class='form-input' type='text' value='" . $coach->coach_country . "' required/>
								</div>
							</div>
							<button type='submit'>Guardar cambios</button>
						</form>";
		return $html;
	}
}

if (!function_exists('render_user_teams')) {
	function render_user_teams() {
		$user_id = get_current_user_id();
		$teams = TeamsUserDatabase::get_teams_by_coach($user_id);

		$html = "<h2 style='text-align: center;'>Mis Equipos</h2>";
		$html .= "<div class='user-teams'>";
		$html .= "<div id='add-team-button' class='add-team-button'>Crear Equipo</div>";
		foreach ($teams as $team) {
			$html .= "<div data-team-id='" . esc_attr($team->team_id) . "' class='team-item'>";
			$html .= "<img class='team-logo' src='" . wp_get_attachment_image_url($team->team_logo, 'full') . "' alt='" . $team->team_name . "' />";
			$html .= "<span id='team-" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</span>";
			$html .= "</div>";
		}
		$html .= "</div>";
		return $html;
	}
}

function render_team_info($team_id) {
	$user_id = get_current_user_id();
	$team = TeamsUserDatabase::get_team_by_id($team_id);
	$is_team_registered = $team->is_registered;
	$is_pending = TeamRegisterQueueDatabase::is_team_pending($team_id);

	$html = "<h2 style='text-align: center;'>" . $team->team_name . "</h2>";
	$html .= "<div class='team-info'>";
	
	$html .= "<div class='team-logo-container'>";
	$html .= "<span>Logo:</span>";
	$html .= "<img src='" . wp_get_attachment_image_url($team->team_logo, 'full') . "' alt='" . $team->team_name . "' />";
	$html .= "</div>";

	
	$html .= "<div class='team-status-container'>";
	$html .= "<span>Estatus:</span>";
	if ($is_team_registered) {
		$team_tournament = TeamsDatabase::get_team_tournament($team_id);
		$tournament  = TournamentsDatabase::get_tournament_by_id($team_tournament->tournament_id);
		$html .= "<span id='team-" . esc_attr($team->team_id) . "'>Registrado en torneo: " . esc_html($tournament->tournament_name) . "</span>";
	} else if ($is_pending) {
		$html .= "<span id='team-" . esc_attr($team->team_id) . "'>Por aprobar</span>";
	} else {
		$html .= "<button id='register-team-button'>Registrar equipo</button>";
	}
	$html .= "</div>";

	$html .= "</div>";
	return $html;
}

function render_user_create_team() {
	$html = "<h2 style='text-align: center;'>Crear Equipo</h2>";
	$html .= "<form id='create-team-form' class='create-team-form'>
							<div class='create-team-form-group'>
								<label for='team_name'>Nombre del equipo:</label>
								<input name='team_name' class='form-input' type='text' placeholder='Nombre del equipo' required/>
							</div>
							<div class='create-team-form-group'>
								<label for='logo'>Logo:</label>
								<div class='logo-container'>
									<input type='file' id='logo' name='logo' required/>
									<div class='logo-preview'>
										<img id='logo-preview' src='#' width='100' height='100' alt='logo' />
									</div>
								</div>
							</div>
							<button type='submit'>Crear Equipo</button>
						</form>";
	return $html;
}

if (!function_exists('render_user_matches')) {
	function render_user_matches() {
		// $user_id = get_current_user_id();
		// $teams = TeamsDatabase::get_teams_by_coach($user_id);

		$html = "<h2 style='text-align: center;'>Partidos</h2>";
    $html .= "<span>No hay partidos que mostrar</span>";
		return $html;
	}
}

if (!function_exists('render_user_tournaments')) {
	function render_user_tournaments() {
		// $user_id = get_current_user_id();
		// $teams = TeamsDatabase::get_teams_by_coach($user_id);
		$tournaments = TournamentsDatabase::get_active_tournaments_not_started(get_current_user_id());

		$html = "<h2 style='text-align: center;'>Torneos</h2>";
    $html .= "<div class='active-tournaments'>";
    foreach ($tournaments as $tournament) {
			$html .= "<div id='tournament-" . esc_attr($tournament->tournament_id) . "' class='tournament-item'>";
			$html .= "<span>" . esc_html($tournament->tournament_name) . "</span>";
			$html .= "<button id='join-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Registrar equipo</button>";
			$html .= "</div>";
    }
    $html .= "</div>";
		return $html;
	}
}

function render_user_join_tournament($tournament_id) {
	$user_id = get_current_user_id();
	$teams = TeamsUserDatabase::get_teams_by_coach($user_id);
	$divisions = DivisionsDatabase::get_divisions_by_tournament($tournament_id);
	$pending_teams = TeamRegisterQueueDatabase::get_teams_by_coach($user_id);

	$html = "<div class='join-tournament-container'>";
	$html .= "<h2 style='text-align: center;'>Unirse a torneo</h2>";
	$html .= "<form id='join-tournament-form' data-tournament-id='" . esc_attr($tournament_id) . "' class='join-tournament-form'>
						<div class='join-tournament-form-group'>
							<label for='team_id'>Equipo:</label>
							<select name='team_id' class='form-input' required>
								<option value=''>Selecciona un equipo</option>";

	foreach ($teams as $team) {
		if ($team->is_registered) {
			continue;
		}
		$is_pending = false;
		foreach ($pending_teams as $pending_team) {
			if ($pending_team->team_id == $team->team_id) {
				$is_pending = true;
				break;
			}
		}
		if ($is_pending) {
			continue;
		}
		$html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
	}	

	$html .= "</select>
						</div>
						<div class='join-tournament-form-group'>
							<label for='division_id'>Division:</label>
							<select name='division_id' class='form-input' required>
								<option value=''>Selecciona una division</option>";
	foreach ($divisions as $division) {
		$html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . "</option>";
	}
	$html .= "</select>
						</div>
						<button type='submit'>Unirse al torneo</button>
					</form>";
	$html .= "</div>";
	return $html;
}

if (!function_exists('render_user_results')) {
	function render_user_results() {
		// $user_id = get_current_user_id();
		// $teams = TeamsDatabase::get_teams_by_coach($user_id);

		$html = "<h2 style='text-align: center;'>Resultados</h2>";
    $html .= "<span>No hay resultados que mostrar</span>";
		return $html;
	}
}

function handle_fetch_team_info() {
	if (!isset($_POST['team_id'])) {
		wp_send_json_error(array('message' => 'Faltan datos'));
		return;
	}
	$team_id = sanitize_text_field($_POST['team_id']);
	wp_send_json_success(array('html' => render_team_info($team_id)));
}

function handle_create_team() {
	$team_name = sanitize_text_field($_POST['team_name']);
	$logo = $_FILES['logo'];
	$user_id = get_current_user_id();
	// upload image to wordpress and get the attachment id to link to team
	$attachment_id = null;
	addImageToWordPressMediaLibrary($logo['tmp_name'], $logo['name'], $logo['name'], $attachment_id);

	$result = TeamsUserDatabase::insert_team($team_name, $user_id, strval($attachment_id));

	if ($result[0]) {
		wp_send_json_success(array('message' => 'Equipo creado exitosamente', 'html' => render_user_teams()));
	}
	wp_send_json_error(array('message' => 'Equipo no creado'));
}

function handle_create_team_screen() {
	wp_send_json_success(array('html' => render_user_create_team()));
}

function handle_register_team() {
	wp_send_json_success(array('html' => render_user_tournaments()));
}

function handle_join_tournament_form() {
	if (!isset($_POST['tournament_id'])) {
		wp_send_json_error(array('message' => 'Faltan datos'));
		return;
	}

	$tournament_id = sanitize_text_field($_POST['tournament_id']);
	
	wp_send_json_success(array('html' => render_user_join_tournament($tournament_id)));
}

function handle_join_tournament() {
	if (!isset($_POST['tournament_id']) || !isset($_POST['division_id']) || !isset($_POST['team_id'])) {
		wp_send_json_error(array('message' => 'Faltan datos'));
		return;
	}

	$tournament_id = sanitize_text_field($_POST['tournament_id']);
	$division_id = sanitize_text_field($_POST['division_id']);
	$team_id = sanitize_text_field($_POST['team_id']);
	$coach_id = get_current_user_id();

	$result = TeamRegisterQueueDatabase::insert_team($tournament_id, $division_id, $coach_id, $team_id);
	if ($result[0]) {
		wp_send_json_success(array('message' => 'Equipo agregado exitosamente', 'html' => render_user_tournaments()));
	}
	wp_send_json_error(array('message' => 'Equipo no agregado'));
}

function handle_complete_profile_form() {
  if (!isset($_POST['name']) || !isset($_POST['last_name']) || !isset($_POST['phone']) || !isset($_POST['city']) || !isset($_POST['state']) || !isset($_POST['country'])) {
    wp_send_json_error(array('message' => 'Faltan datos'));
    return;
  }
    $coach_name = sanitize_text_field($_POST['name']);
    $coach_last_name = sanitize_text_field($_POST['last_name']);
    $coach_contact = sanitize_text_field($_POST['phone']);
    $coach_city = sanitize_text_field($_POST['city']);
    $coach_state = sanitize_text_field($_POST['state']);
    $coach_country = sanitize_text_field($_POST['country']);

    $coach_full_name = $coach_name . " " . $coach_last_name;
    $coach_id = get_current_user_id();
    $result= CoachesUserDatabase::insert_coach(
      $coach_id, 
      $coach_full_name, 
      $coach_contact, 
      $coach_city, 
      $coach_state, 
      $coach_country
    );
    
    if ($result[0]) {
      wp_send_json_success(array('message' => 'Profile saved successfully!', 'html' => render_profile_menu() . render_user_data()));
    } 
    wp_send_json_error(array('message' => 'Profile not saved'));
}

function handle_update_profile() {
    if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['city']) || !isset($_POST['state']) || !isset($_POST['country'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }
    $coach_name = sanitize_text_field($_POST['name']);
    $coach_contact = sanitize_text_field($_POST['phone']);
    $coach_city = sanitize_text_field($_POST['city']);
    $coach_state = sanitize_text_field($_POST['state']);
    $coach_country = sanitize_text_field($_POST['country']);
    $coach_id = get_current_user_id();
    $result= CoachesUserDatabase::update_coach(
      $coach_id, 
      $coach_name, 
      $coach_contact, 
      $coach_city, 
      $coach_state, 
      $coach_country
    );
    
    if ($result[0]) {
			$coach_instances = CoachesDatabase::get_coaches_by_coach_user($coach_id);
			foreach ($coach_instances as $coach_instance) {
				CoachesDatabase::update_coach(
					$coach_instance->coach_id, 
					$coach_name, 
					$coach_contact, 
					$coach_city, 
					$coach_state, 
					$coach_country,
					true
				);
			}

      wp_send_json_success(array('message' => 'Profile saved successfully!', 'html' => render_user_data()));
    } 
    wp_send_json_error(array('message' => 'Profile not saved'));
}

function handle_switch_menu() {
    if (!isset($_POST['menu'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }

    $menu = sanitize_text_field($_POST['menu']);
    match ($menu) {
        'profile' => wp_send_json_success(array('html' => render_user_data())),
        'teams' => wp_send_json_success(array('html' => render_user_teams())),
        'matches' => wp_send_json_success(array('html' => render_user_matches())),
        'tournaments' => wp_send_json_success(array('html' => render_user_tournaments())),
        'results' => wp_send_json_success(array('html' => render_user_results())),
        default => wp_send_json_error(array('message' => 'Menu no encontrado')),
    };
}

add_action('wp_ajax_handle_register_team', 'handle_register_team');
add_action('wp_ajax_nopriv_handle_register_team', 'handle_register_team'); // for non-logged-in users
add_action('wp_ajax_handle_update_profile', 'handle_update_profile');
add_action('wp_ajax_nopriv_handle_update_profile', 'handle_update_profile'); // for non-logged-in users
add_action('wp_ajax_handle_fetch_team_info', 'handle_fetch_team_info');
add_action('wp_ajax_nopriv_handle_fetch_team_info', 'handle_fetch_team_info'); // for non-logged-in users
add_action('wp_ajax_handle_join_tournament', 'handle_join_tournament');
add_action('wp_ajax_nopriv_handle_join_tournament', 'handle_join_tournament'); // for non-logged-in users
add_action('wp_ajax_handle_join_tournament_form', 'handle_join_tournament_form');
add_action('wp_ajax_nopriv_handle_join_tournament_form', 'handle_join_tournament_form'); // for non-logged-in users
add_action('wp_ajax_handle_create_team', 'handle_create_team');
add_action('wp_ajax_nopriv_handle_create_team', 'handle_create_team'); // for non-logged-in users
add_action('wp_ajax_handle_create_team_screen', 'handle_create_team_screen');
add_action('wp_ajax_nopriv_handle_create_team_screen', 'handle_create_team_screen'); // for non-logged-in users
add_action('wp_ajax_handle_switch_menu', 'handle_switch_menu');
add_action('wp_ajax_nopriv_handle_switch_menu', 'handle_switch_menu'); // for non-logged-in users
add_action('wp_ajax_handle_complete_profile_form', 'handle_complete_profile_form');
add_action('wp_ajax_nopriv_handle_complete_profile_form', 'handle_complete_profile_form'); // for non-logged-in users
