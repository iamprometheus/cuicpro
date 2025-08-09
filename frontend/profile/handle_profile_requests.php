<?php

require_once __DIR__ . '/handle_profile_matches_requests.php';

if (!function_exists('render_user_data')) {
	function render_user_data() {
		$user = wp_get_current_user();
		$user_role = $user->roles;

		$user_data = null;
		if (in_array("coach", $user_role)) {
			$user_data = CoachesUserDatabase::get_coach_by_id($user->ID);
		} else if (in_array("player", $user_role)) {
			$user_data = PlayersUserDatabase::get_player_by_id($user->ID);
		} else if (in_array("official", $user_role)) {
			$user_data = OfficialsUserDatabase::get_official_by_id($user->ID);
		}

		$html = "<h2 style='text-align: center;'>Mis Datos</h2>";
		$html .= "<form id='user-data-form' class='user-data-form'>
							<div class='form-group'>
								<label for='name'>ID:</label>
								<input name='name' class='form-input' type='text' value='" . $user_data->user_id . "' disabled/>
							</div>
							<div class='form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' value='" . $user_data->user_name . "' required/>
							</div>
							<div class='form-group'>
								<label for='phone'>Telefono:</label>
								<input name='phone' class='form-input' type='text' value='" . $user_data->user_contact . "' required/>
							</div>
							<div class='form-group'>
								<label for='location'>Ubicacion:</label>
								<div class='multiple-fields-container'>
									<input name='city' class='form-input' type='text' value='" . $user_data->user_city . "' required/>
									<input name='state' class='form-input' type='text' value='" . $user_data->user_state . "' required/>
									<input name='country' class='form-input' type='text' value='" . $user_data->user_country . "' required/>
								</div>
							</div>
							<button type='submit'>Guardar cambios</button>
						</form>";
		return $html;
	}
}

if (!function_exists('render_profile_menu')) {
	function render_profile_menu() {
		$user = wp_get_current_user();
		$user_roles = $user->roles;

		$html = "";
		$html .= "<div class='profile-menu'>";
		$html .= "<div class='profile-menu-item active' id='profile'>
								<span>Mis Datos</span>
							</div>";
		if (in_array("coach", $user_roles)) {
			$html .= "<div class='profile-menu-item' id='teams'>
								<span>Mis Equipos</span>
							</div>";
		}
		if (in_array("player", $user_roles)) {
			$html .= "<div class='profile-menu-item' id='my-team'>
								<span>Mi Equipo</span>
							</div>";
			$html .= "<div class='profile-menu-item' id='my-matches'>
								<span>Mis Partidos</span>
							</div>";
		}
		
		if (in_array("coach", $user_roles) || in_array("official", $user_roles)) {
			$html .= "<div class='profile-menu-item' id='matches'>
								<span>Partidos</span>
							</div>";
			$html .= "<div class='profile-menu-item' id='tournaments'>
									<span>Torneos</span>
								</div>";
		}
		$html .= "<div class='profile-menu-item' id='results'>
								<span>Resultados</span>
							</div>";
		$html .= "</div>";
		return $html;
	}
}

if (!function_exists('render_complete_profile_form')) {
	function render_complete_profile_form($has_profile) {
		if ($has_profile) {
			return render_profile_menu();
		}
		// profile completion form
		$html = "<div class='complete-profile-container'>";
		$html .= "<h2 style='text-align: center;'>Completar registro</h2>";
		$html .= "<div class='complete-profile-form-container'>";
		$html .= "<form id='complete-profile-form' class='complete-profile-form'>
							<div class='form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' placeholder='Nombre' required/>
							</div>
							<div class='form-group'>
								<label for='last_name'>Apellido:</label>
								<input name='last_name' class='form-input' type='text' placeholder='Apellido' required/>
							</div>
							<div class='form-group'>
								<label for='phone'>Telefono:</label>
								<input name='phone' class='form-input' type='text' placeholder='Telefono' required/>
							</div>
							<div class='form-group'>
								<label for='location'>Ubicacion:</label>
								<div class='multiple-fields-container'>
									<input name='city' class='form-input' type='text' placeholder='Ciudad' required/>
									<input name='state' class='form-input' type='text' placeholder='Estado' required/>
									<input name='country' class='form-input' type='text' placeholder='Pais' required/>
								</div>
							</div>
							<div class='form-group'>
								<label for='account_type'>Soy: </label>
								<div class='multiple-fields-container'>
									<input id='account-coach' name='account_type' class='form-input-radio' type='radio' checked value='coach'/>
									<label for='account-coach' class='form-input-radio-label'>Coach</label>";

									// <input id='account-player' name='account_type' class='form-input-radio' type='radio' value='player'/>
									// <label for='account-player' class='form-input-radio-label'>Jugador</label>
									// <input id='account-official' name='account_type' class='form-input-radio' type='radio' value='official'/>
									// <label for='account-official' class='form-input-radio-label'>Arbitro</label>
		$html .=		"</div>
							</div>
							<button type='submit'>Guardar y continuar</button>
						</form>
					</div>
				</div>";
		return $html;
	}
}

if (!function_exists('render_slot')) {
	function render_slot($has_profile) {
		if ($has_profile) {
			$html = "";
			$html .= "<div class='user-data-container'>";
			$html .= render_user_data();
			$html .= "</div>";
			return $html;
		}
		return "";
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
			$html .= "<div data-team-id='" . esc_attr($team->team_id) . "' id='team-item' class='team-item'>";
			$html .= "<img class='team-logo' src='" . wp_get_attachment_image_url($team->team_logo, 'full') . "' alt='" . $team->team_name . "' />";
			$html .= "<span id='team-" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</span>";
			$html .= "</div>";
		}
		$html .= "</div>";
		return $html;
	}
}

// only players
function render_user_team() {
	$user_id = get_current_user_id();
	$player_user = PlayersUserDatabase::get_player_by_id($user_id);
	if (!$player_user->user_has_team) {
		return render_player_join_team();
	}

	$player = PlayersDatabase::get_player_by_user_id($user_id);
	$team_id = $player->team_id;
	$team = TeamsUserDatabase::get_team_by_id($team_id);
	$is_team_registered = $team->is_registered;
	$is_pending = TeamRegisterQueueDatabase::is_team_pending($team_id);

	$html = "<div class='info-header'>
						<h2>" . $team->team_name . "</h2>
					</div>";
	$html .= "<div class='team-info'>";
	$html .= "<div>";
	$html .= "<span id='team-id' data-team-id='" . esc_attr($team_id) . "'>ID: " . esc_html($team_id) . "</span>";
	$html .= "</div>";
	$html .= "<div class='team-logo-container'>";
	$html .= "<span>Logo:</span>";
	$html .= "<img src='" . wp_get_attachment_image_url($team->team_logo, 'full') . "' alt='" . $team->team_name . "' />";
	$html .= "</div>";

	$html .= "<div class='team-status-container'>";
	$html .= "<span>Estatus:</span>";
	if ($is_team_registered) {
		$team_tournament = TeamsDatabase::get_team_tournament($team_id);
		$tournament  = TournamentsDatabase::get_tournament_by_id($team_tournament->tournament_id);
		$division_name = DivisionsDatabase::get_division_by_id($team_tournament->division_id)->division_name;
		$html .= "<span id='team-" . esc_attr($team_id) . "'>Registrado en torneo: " . esc_html($tournament->tournament_name) . ", Division: " . esc_html($division_name) . "</span>";
	} else if ($is_pending) {
		$html .= "<span id='team-" . esc_attr($team_id) . "'>Por aprobar</span>";
	} else {
		$html .= "<span>Pendiente de registro en torneo</span>";
	}
	$html .= "</div>";

	$players = PlayersDatabase::get_players_by_team($team_id);

	$html .= "<div class='team-players'>";
	$html .= "<h2 style='text-align: center;'>Jugadores</h2>";
	$html .= "<div class='user-players'>";
	foreach ($players as $player) {
		$html .= "<div data-player-id='" . esc_attr($player->player_id) . "' class='player-item'>";
		$html .= "<img class='player-photo' src='" . wp_get_attachment_image_url($player->player_photo, 'full') . "' alt='" . $player->player_name . "' />";
		$html .= "<span id='player-" . esc_attr($player->player_id) . "'>" . esc_html($player->player_name) . "</span>";
		$html .= "</div>";
	}
	$html .= "</div>";
	$html .= "</div>";

	$html .= "</div>";
	return $html;
}

function render_user_add_player($team_id) {
	$team = TeamsUserDatabase::get_team_by_id($team_id);
	$html = "<div class='info-header'>
						<span id='back-button' data-screen='user-team-info' data-team-id='" . esc_attr($team_id) . "'>Volver</span>
						<h2>Agregar Jugador</h2>
					</div>";
	$html .= "<h3 style='text-align: center;' data-team-id='" . esc_attr($team_id) . "' id='team-id-form'>Equipo: " . $team->team_name . "</h3>";
	$html .= "<form id='add-player-form' class='create-team-form'>
							<div class='create-team-form-group'>
								<label for='player_name'>Nombre del jugador:</label>
								<input name='player_name' class='form-input' type='text' placeholder='Nombre del jugador' required/>
							</div>
							<div class='create-team-form-group'>
								<label for='logo'>Foto:</label>
								<div class='logo-container'>
									<input type='file' id='logo' name='logo' required/>
									<div class='logo-preview'>
										<img id='logo-preview' src='#' width='100' height='100' alt='Foto' />
									</div>
								</div>
							</div>
							<button type='submit'>Agregar Jugador</button>
						</form>";
	return $html;
}

function render_player_join_team() {
	$coaches = CoachesUserDatabase::get_coaches();
	$html = "<div class='info-header'>
						<h2>Unirse a un equipo</h2>
					</div>";
	$html .= "<form id='join-team-form' class='create-team-form'>
							<div class='create-team-form-group'>
								<label for='logo'>Mi Foto:</label>
								<div class='logo-container'>
									<input type='file' id='logo' name='logo' required/>
									<div class='logo-preview'>
										<img id='logo-preview' src='#' width='100' height='100' alt='Foto' />
									</div>
								</div>
							</div>
							<div class='create-team-form-group'>
								<label for='coach_id'>Mi Coach:</label>
								<select name='coach_id' id='coach_dropdown_id' class='form-input' required>
									<option value=''>Selecciona tu coach</option>";
									foreach ($coaches as $coach) {
										$html .= "<option value='" . esc_attr($coach->user_id) . "'>" . esc_html($coach->user_name) . "</option>";
									}
	$html .= "    </select>
							</div>
							<div class='create-team-form-group'>
								<label for='team_id'>Equipo:</label>
								<select name='team_id' id='team_dropdown_id' class='form-input' required>
									<option value=''>Selecciona el equipo</option>";
									
	$html .= "    </select>
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

	wp_send_json_success(array('html' => $html));
}

// only coaches
function render_team_players($team_id) {
	$players = PlayersDatabase::get_players_by_team($team_id);

	$html = "<div class='team-players'>";
	$html .= "<h2 style='text-align: center;'>Jugadores</h2>";
	$html .= "<div class='user-players'>";
	$html .= "<div id='add-player-button' class='add-player-button'>Agregar Jugador</div>";
	foreach ($players as $player) {
		$html .= "<div data-player-id='" . esc_attr($player->player_id) . "' id='player-data' class='player-item'>";
		$html .= "<img class='player-photo' src='" . wp_get_attachment_image_url($player->player_photo, 'full') . "' alt='" . $player->player_name . "' />";
		$html .= "<span id='player-" . esc_attr($player->player_id) . "'>" . esc_html($player->player_name) . "</span>";
		$html .= "</div>";
	}
	$html .= "</div>";
	$html .= "</div>";
	return $html;
}

function render_player_info($player_id) {
	$player = PlayersDatabase::get_player_by_id($player_id);

	$html = "<div class='info-header'>
						<span id='back-button' data-screen='user-team-info' data-team-id='" . esc_attr($player->team_id) . "'>Volver</span>
						<h2>Actualizar información de jugador</h2>
					</div>";
	$html .= "<form id='player-data-form' class='user-data-form'>
						<div class='form-group'>
							<label for='player_id'>ID:</label>
							<input name='player_id' id='player_id' class='form-input' type='text' value='" . $player->player_id . "' disabled/>
						</div>
						<div class='form-group'>
							<label for='player_name'>Nombre:</label>
							<input name='player_name' class='form-input' type='text' value='" . $player->player_name . "' required/>
						</div>
						<div class='create-team-form-group'>
								<label for='logo'>Foto:</label>
								<div class='logo-container'>
									<input type='file' id='logo' name='logo'/>
									<div class='logo-preview'>
										<img id='logo-preview' src='" . wp_get_attachment_image_url($player->player_photo, 'full') . "' width='100' height='100' alt='Foto' />
									</div>
								</div>
							</div>
						<button type='submit'>Guardar cambios</button>
						<button id='delete-player-button' type='button' data-player-id='" . esc_attr($player->player_id) . "' data-team-id='" . esc_attr($player->team_id) . "'>Eliminar Jugador</button>
					</form>";
	return $html;
}

function render_team_info($team_id) {
	$team = TeamsUserDatabase::get_team_by_id($team_id);
	$is_team_registered = $team->is_registered;
	$is_pending = TeamRegisterQueueDatabase::is_team_pending($team_id);

	$status = "";
	if ($is_team_registered) {
		$team_tournament = TeamsDatabase::get_team_tournament($team_id);
		$tournament  = TournamentsDatabase::get_tournament_by_id($team_tournament->tournament_id);
		$division_name = DivisionsDatabase::get_division_by_id($team_tournament->division_id)->division_name;
		$status .= "Registrado en torneo: " . esc_html($tournament->tournament_name) . ", Division: " . esc_html($division_name);
	} else if ($is_pending) {
		$status .= "Por aprobar";
	} else {
		$status .= "Sin registrar";
	}

	$html = "<div class='info-header'>
						<span id='back-button' data-screen='user-teams'>Volver</span>
						<h2>" . $team->team_name . "</h2>
					</div>";
	$html .= "<div class='team-info'>";
	$html .= "<h2 style='text-align: center;'>Información del equipo</h2>";
	$html .= "<form id='team-data-form' class='user-data-form'>
							<div class='form-group'>
								<label for='id'>ID:</label>
								<input name='id' id='team-id' data-team-id='" . $team->team_id . "' class='form-input' type='text' value='" . $team->team_id . "' disabled/>
							</div>
							<div class='form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' value='" . $team->team_name . "' required/>
							</div>
							
							<div class='form-group'>
								<label for='name'>Estatus:</label>
							  <span class='form-input' style='align-items: center; display: flex; font-size: 14px; font-weight: 600;'>" . $status . "</span>
							</div>

							<div class='create-team-form-group'>
								<label for='logo'>Logo:</label>
								<div class='logo-container'>
									<input type='file' id='logo' name='logo'/>
									<div class='logo-preview'>
										<img id='logo-preview' src='" . wp_get_attachment_image_url($team->team_logo, 'full') . "' width='100' height='100' alt='Foto' />
									</div>
								</div>
							</div>
							<button type='submit'>Guardar cambios</button>
						</form>";

	$html .= render_team_players($team_id);

	$delete_disabled = "";
	$cancel_registration_disabled = "";
	if ($is_team_registered) {
		$delete_disabled = "disabled";
		$cancel_registration_disabled = "disabled";
	} else if (!$is_pending) {
		$cancel_registration_disabled = "disabled";
	}

	$html .= "<hr style='border: 2px solid black; width: 100%;'/>";
	$html .= "<h2 style='text-align: center;'>Zona de Peligro</h2>";
	$html .= "<div class='action-buttons-container'>";
	$html .= "<button class='danger-button' id='show-cancel-registration-dialog' type='button' $cancel_registration_disabled data-team-id='" . esc_attr($team->team_id) . "'>Cancelar Registro</button>";
	$html .= "<button class='danger-button' id='show-delete-team-dialog' type='button' $delete_disabled data-team-id='" . esc_attr($team->team_id) . "'>Eliminar Equipo</button>";
	$html .= "<dialog id='cancel-registration-dialog'>";
	$html .= "<p>¿Estás seguro de cancelar el registro?</p>";
	$html .= "<div class='action-buttons-container'>";
	$html .= "<button id='cancel-cancel-registration-button' type='button' >Volver</button>";
	$html .= "<button id='confirm-cancel-registration-button' type='button' class='danger-button' $cancel_registration_disabled data-team-id='" . esc_attr($team->team_id) . "'>Cancelar Registro</button>";
	$html .= "</div>";
	$html .= "</dialog>";
	$html .= "<dialog id='delete-team-dialog'>";
	$html .= "<p>¿Estás seguro de eliminar el equipo? Esta acción no se puede deshacer.</p>";
	$html .= "<div class='action-buttons-container'>";
	$html .= "<button id='cancel-delete-team-button' type='button'>Volver</button>";
	$html .= "<button id='confirm-delete-team-button' type='button' class='danger-button' $delete_disabled data-team-id='" . esc_attr($team->team_id) . "'>Eliminar Equipo</button>";
	$html .= "</div>";
	$html .= "</dialog>";
	$html .= "</div>";

	$html .= "</div>";
	return $html;
}

function render_user_create_team() {
	$html = "<div class='info-header'>
						<span id='back-button' data-screen='user-teams'>Volver</span>
						<h2>Crear Equipo</h2>
					</div>";
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

if (!function_exists('render_user_tournaments')) {
	function render_user_tournaments() {
		// $user_id = get_current_user_id();
		// $teams = TeamsDatabase::get_teams_by_coach($user_id);
		$tournaments = TournamentsDatabase::get_active_tournaments_not_started(get_current_user_id());

		$html = "<h2 style='text-align: center;'>Torneos</h2>";
    $html .= "<div class='active-tournaments'>";
    foreach ($tournaments as $tournament) {
			$html .= "<div id='active-tournament' class='tournament-item-fe'>";
			$html .= "<span>" . esc_html($tournament->tournament_name) . "</span>";
			$html .= "<button id='join-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Registrar equipo</button>";
			$html .= "</div>";
    }

		if (empty($tournaments)) {
			$html .= "<h2>No hay torneos activos para registrar equipos</h2>";
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
	$html .= "<div class='info-header'>
						<span id='back-button' data-screen='user-tournaments'>Volver</span>
						<h2>Unirse a torneo</h2>
					</div>";
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
    $mode = ModesDatabase::get_mode_by_id($division->division_mode);
    $category = CategoriesDatabase::get_category_by_id($division->division_category);
		$html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . " " . $mode->mode_description . " " . $category->category_description . "</option>";
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
		$user_id = get_current_user_id();
		$coach_instances = CoachesDatabase::get_coaches_by_coach_user($user_id);

		$coach_instances = array_reverse($coach_instances);
		$tournaments = [];
		foreach ($coach_instances as $coach_instance) {
			$tournament_id = $coach_instance->tournament_id;
			if (in_array($tournament_id, $tournaments)) {
				continue;
			}
			$tournaments[] = $tournament_id;
		}

		$html = "<h2 style='text-align: center;'>Mis Torneos</h2>";
    $html .= "<div class='user-tournaments'>";
    foreach ($tournaments as $tournament_id) {
			$tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
			$html .= "<div id='tournament-played' data-tournament-id='" . esc_attr($tournament_id) . "' class='tournament-item-fe'>";
			$html .= "<span>" . esc_html($tournament->tournament_name) . "</span>";
			$html .= "</div>";
    }
    $html .= "</div>";
		return $html;
	}
}

function render_coach_teams_in_tournament($tournament_id) {
	$user_id = get_current_user_id();

	$coach = CoachesDatabase::get_coach_by_coach_user_and_tournament($user_id, $tournament_id);
	$tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
	$teams = TeamsDatabase::get_teams_by_coach($coach->coach_id);
	$html = "";
	
	$html .= "<div class='info-header'>
							<span id='back-button' data-screen='user-results'>Volver</span>
							<h2>Mis Equipos en el torneo: " . esc_html($tournament->tournament_name) . "</h2>
						</div>";

	if (empty($teams)) {
		$html .= "<h2>No tienes equipos en este torneo</h2>";
		return $html;
	}
	
	$html .= "<div class='user-teams'>";
	
	foreach ($teams as $team) {
		if (!$team->division_id) continue;
		$division_name = DivisionsDatabase::get_division_by_id($team->division_id)->division_name;
		$html .= "<div data-team-id='" . esc_attr($team->team_id) . "' id='team-item-results' class='team-item-results'>";
		$html .= "<img class='team-logo' src='" . wp_get_attachment_image_url($team->logo, 'full') . "' alt='" . $team->team_name . "' />";
		$html .= "<span>" . esc_html($team->team_name) . "</span>";
		$html .= "<span style='font-size: 12px;'>" . esc_html($division_name) . "</span>";
		$html .= "</div>";
	}
	$html .= "</div>";
	return $html;
}

function render_team_and_division_results($team_id) {
	$tournament_id = TeamsDatabase::get_team_by_id($team_id)->tournament_id;
	$html = "";
	$html .= "<div class='info-header'>
						<span id='back-button' data-tournament-id='" . esc_attr($tournament_id) . "' data-screen='coach-teams-in-tournament'>Volver</span>
						<h2>Resultados del equipo</h2>
					</div>";

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
	$html .= render_leaderboard_table($bracket->bracket_id);
	$html .= "<hr/>";

	$playoffs = render_playoffs_results($bracket->bracket_id);

	$html .= $playoffs['html'];
	$html .= "</div>";
	$html .= "</div>";
	$html .= "</div>";
	return ['html' => $html, 'elements' => $playoffs['elements']];
}

function render_playoffs_results(int $bracket_id) {
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
          $html .= create_bracket_result($match);
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

function create_bracket_result($match) {
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  $match_time = $match->match_time . ":00";
	$official_name = OfficialsDatabase::get_official_by_id($match->official_id)->official_name;
	if (!$official_name) {
		$official_name = "Arbitro no asignado";
	}

	$team_1_winner = "";
	$team_2_winner = "";
	if (!$match->match_pending) {
		$match_info = MatchesDatabase::get_match_by_pending_match_id($match->match_id);
		if ($match_info->match_winner == $match_info->team_id_1) {
			$team_1_winner = "match-winner";
		} else {
			$team_2_winner = "match-winner";
		}
	}

  $html = "<div class='bracket-match'>";
  $html .= "<span class='" . $team_1_winner . "'>" . $team_1_name . "</span>";
	$html .= "<hr/>";
  $html .= "<span class='" . $team_2_winner . "'>" . $team_2_name . "</span>";
  $html .= "</div>";
  $html .= "<hr class='hidden' />";
	$html .= "<div class='match-info' hidden>";
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
  
  $html .= "<hr />";

  if (!$match->match_pending) {
    $match_info = MatchesDatabase::get_match_by_pending_match_id($match->match_id);
    $html .= "<div class='match-data-end-data'>";
    $html .= "<span>Resultados</span>";
    $html .= "<span> Ganador: " . TeamsDatabase::get_team_by_id($match_info->match_winner)->team_name . " </span>";
    $html .= "<span> Anotaciones equipo " . TeamsDatabase::get_team_by_id($match_info->team_id_1)->team_name . ": " . $match_info->goals_team_1 . " </span>";
    $html .= "<span> Anotaciones equipo " . TeamsDatabase::get_team_by_id($match_info->team_id_2)->team_name . ": " . $match_info->goals_team_2 . " </span>";
    $html .= "</div>";
		$html .= "</div>";
    return $html;
  } else {
    $html .= "<div class='match-data-end-data'>";
    $html .= "<span>Resultados</span>";
    $html .= "<span>Ganador: Por definir </span>";
    $html .= "</div>";
		$html .= "</div>";
    return $html;
  }
  
	$html .= "</div>";
  return $html;
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

	$result = TeamsUserDatabase::insert_team($team_name, $user_id, $attachment_id);

	if ($result[0]) {
		wp_send_json_success(array('message' => 'Equipo creado exitosamente', 'html' => render_user_teams()));
	}
	wp_send_json_error(array('message' => 'Equipo no creado'));
}

function handle_add_player() {
	if (!isset($_POST['team_id']) || !isset($_POST['player_name'])) {
		wp_send_json_error(array('message' => 'Faltan datos'));
		return;
	}
	$team_id = sanitize_text_field($_POST['team_id']);
	$player_name = sanitize_text_field($_POST['player_name']);
	$logo = $_FILES['logo'];
	$coach_id = get_current_user_id();
	// upload image to wordpress and get the attachment id to link to team
	$attachment_id = null;
	addImageToWordPressMediaLibrary($logo['tmp_name'], $logo['name'], $logo['name'], $attachment_id);

	$result = PlayersDatabase::insert_player(null, $player_name, $team_id, $attachment_id, $coach_id);

	if ($result[0]) {
		wp_send_json_success(array('message' => 'Jugador creado exitosamente', 'html' => render_team_info($team_id)));
	}
	wp_send_json_error(array('message' => 'Jugador no creado'));
}

function handle_create_team_screen() {
	wp_send_json_success(array('html' => render_user_create_team()));
}

function handle_add_player_screen() {
	if (!isset($_POST['team_id'])) {
		wp_send_json_error(array('message' => 'Faltan datos'));
		return;
	}
	$team_id = sanitize_text_field($_POST['team_id']);
	wp_send_json_success(array('html' => render_user_add_player($team_id)));
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
  if (!isset($_POST['name']) || !isset($_POST['last_name']) || !isset($_POST['phone']) || !isset($_POST['city']) || !isset($_POST['state']) || !isset($_POST['country']) || !isset($_POST['account_type'])) {
    wp_send_json_error(array('message' => 'Faltan datos'));
    return;
  }
    $user_name = sanitize_text_field($_POST['name']);
    $user_last_name = sanitize_text_field($_POST['last_name']);
    $user_contact = sanitize_text_field($_POST['phone']);
    $user_city = sanitize_text_field($_POST['city']);
    $user_state = sanitize_text_field($_POST['state']);
    $user_country = sanitize_text_field($_POST['country']);
    $user_account_type = sanitize_text_field($_POST['account_type']);

    $user_full_name = $user_name . " " . $user_last_name;
    $user = wp_get_current_user();

		$result = [false, null];

		if ($user_account_type == 'coach') {
			$result = CoachesUserDatabase::insert_coach(
				$user->ID, 
				$user_full_name, 
				$user_contact, 
				$user_city, 
				$user_state, 
				$user_country,
				$user_account_type
			);
			if ($result[0]) {
				$user->add_role('coach');
			}
		}
		else if ($user_account_type == 'player') {
			$result = PlayersUserDatabase::insert_player(
				$user->ID, 
				$user_full_name, 
				$user_contact, 
				$user_city, 
				$user_state, 
				$user_country,
				$user_account_type
			);
			if ($result[0]) {
				$user->add_role('player');
			}
		} else if ($user_account_type == 'official') {
			$result = OfficialsUserDatabase::insert_official(
				$user->ID, 
				$user_full_name, 
				$user_contact, 
				$user_city, 
				$user_state, 
				$user_country,
				$user_account_type
			);
			if ($result[0]) {
				$user->add_role('official');
			}
		} else {
			wp_send_json_error(array('message' => 'Tipo de cuenta no encontrado'));
		}
    
    if ($result[0]) {
      wp_send_json_success(array('message' => 'Perfil guardado exitosamente!', 'html' => render_profile_menu() . render_slot(true)));
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

function handle_coach_teams_in_tournament() {
    if (!isset($_POST['tournament_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }
    $tournament_id = sanitize_text_field($_POST['tournament_id']);
    wp_send_json_success(array('html' => render_coach_teams_in_tournament($tournament_id)));
}

function handle_results_for_team() {
    if (!isset($_POST['team_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }
    $team_id = sanitize_text_field($_POST['team_id']);
		$result = render_team_and_division_results($team_id);
    wp_send_json_success(array('html' => $result['html'], 'elements' => $result['elements']));
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
        'matches' => wp_send_json_success(array('html' => render_teams_for_matches())),
        'tournaments' => wp_send_json_success(array('html' => render_user_tournaments())),
        'results' => wp_send_json_success(array('html' => render_user_results())),
				'my-team' => wp_send_json_success(array('html' => render_user_team())),
				'my-matches' => wp_send_json_success(array('html' => render_matches_by_team_player())),
        default => wp_send_json_error(array('message' => 'Menu no encontrado')),
    };
}

function handle_user_logged_in() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'No estas logueado'));
        return;
    }

    $roles = ["coach", "player", "official"];

		$user = wp_get_current_user();
		$user_role = $user->roles;

		$has_profile = false;
		if (count(array_intersect($user_role, $roles)) >= 1) {
			$has_profile = true;
		}

		$html = render_complete_profile_form($has_profile);
		$html .= render_slot($has_profile);

    wp_send_json_success(array('html' => $html));
}

function handle_fetch_player_info() {
    if (!isset($_POST['player_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }

    $player_id = sanitize_text_field($_POST['player_id']);
    wp_send_json_success(array('html' => render_player_info($player_id)));
}

function handle_join_team_form() {
		$coach_id = sanitize_text_field($_POST['coach_id']);
    $team_id = sanitize_text_field($_POST['team_id']);
		$player_photo = $_FILES['logo'];

		$user_id = get_current_user_id();
		$player_name = PlayersUserDatabase::get_player_by_id($user_id)->user_name;

		$attachment_id = null;
		addImageToWordPressMediaLibrary($player_photo['tmp_name'], $player_photo['name'], $player_photo['name'], $attachment_id);
		PlayersDatabase::insert_player($user_id, $player_name, $team_id, $attachment_id, $coach_id);
		PlayersUserDatabase::update_player_has_team($user_id, true);

    wp_send_json_success(array('html' => render_user_team()));
}

function handle_update_player() {
		$team_id = sanitize_text_field($_POST['team_id']);
    $player_id = sanitize_text_field($_POST['player_id']);
    $player_name = sanitize_text_field($_POST['player_name']);
		$player_photo = $_FILES['logo'];

		if ($player_photo['name'] != "") {
			$attachment_id = null;
			addImageToWordPressMediaLibrary($player_photo['tmp_name'], $player_photo['name'], $player_photo['name'], $attachment_id);
			PlayersDatabase::update_player($player_id, $player_name, $attachment_id);
		} else {
			PlayersDatabase::update_player_name($player_id, $player_name);
		}

    wp_send_json_success(array('html' => render_team_info($team_id)));
}

function handle_delete_player() {
    if (!isset($_POST['player_id']) || !isset($_POST['team_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }

    $player_id = sanitize_text_field($_POST['player_id']);
    $team_id = sanitize_text_field($_POST['team_id']);

    PlayersDatabase::delete_player($player_id);
    wp_send_json_success(array('html' => render_team_info($team_id)));
}

function handle_update_team() {
    if (!isset($_POST['team_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }

    $team_id = sanitize_text_field($_POST['team_id']);
    $team_name = sanitize_text_field($_POST['name']);
    $team_logo = $_FILES['logo'];

		if ($team_logo['name'] != "") {
        $attachment_id = null;
        addImageToWordPressMediaLibrary($team_logo['tmp_name'], $team_logo['name'], $team_logo['name'], $attachment_id);
        TeamsUserDatabase::update_team($team_id, $team_name, $attachment_id);
				TeamsDatabase::update_teams_name_and_logo($team_id, $team_name, $attachment_id);
    } else {
        TeamsUserDatabase::update_team_name($team_id, $team_name);
				TeamsDatabase::update_teams_name($team_id, $team_name);
    }

    wp_send_json_success(array('html' => render_team_info($team_id)));
}

function handle_delete_team() {
    if (!isset($_POST['team_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }

    $team_id = sanitize_text_field($_POST['team_id']);

    TeamsUserDatabase::delete_team($team_id);
    wp_send_json_success(array('html' => render_user_teams()));
}

function handle_cancel_registration() {
    if (!isset($_POST['team_id'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }

    $team_id = sanitize_text_field($_POST['team_id']);

		$is_pending = TeamRegisterQueueDatabase::is_team_pending($team_id);

    if ($is_pending) {
			$registration = TeamRegisterQueueDatabase::get_registration_by_team_id($team_id);
      TeamRegisterQueueDatabase::delete_team($registration->team_register_queue_id);
    } else {
      wp_send_json_error(array('message' => 'No hay registro pendiente'));
      return;
    }
    wp_send_json_success(array('html' => render_team_info($team_id)));
}

function handle_back_button() {
    if (!isset($_POST['screen'])) {
      wp_send_json_error(array('message' => 'Faltan datos'));
      return;
    }
		if (!isset($_POST['team_id'])) {
			wp_send_json_error(array('message' => 'Faltan datos'));
			return;
		}
		if (!isset($_POST['tournament_id'])) {
			wp_send_json_error(array('message' => 'Faltan datos'));
			return;
		}

    $screen = sanitize_text_field($_POST['screen']);
    $team_id = sanitize_text_field($_POST['team_id']);
		$tournament_id = sanitize_text_field($_POST['tournament_id']);

    match ($screen) {
        'user-teams' => wp_send_json_success(array('html' => render_user_teams())),
        'user-team-info' => wp_send_json_success(array('html' => render_team_info($team_id))),
				'user-tournaments' => wp_send_json_success(array('html' => render_user_tournaments())),
				'user-results' => wp_send_json_success(array('html' => render_user_results())),
				'user-teams-matches' => wp_send_json_success(array('html' => render_teams_for_matches())),
				'coach-teams-in-tournament' => wp_send_json_success(array('html' => render_coach_teams_in_tournament($tournament_id))),
        default => wp_send_json_error(array('message' => render_user_data())),
    };
}

function user_logged_in() {
	if (is_user_logged_in()) {
			wp_send_json_success(array('message' => 'User is logged in!'));
	} else {
			wp_send_json_error(array('message' => 'User is not logged in!'));
	}
}

add_action('wp_ajax_handle_delete_team', 'handle_delete_team');
add_action('wp_ajax_nopriv_handle_delete_team', 'handle_delete_team'); // for non-logged-in users
add_action('wp_ajax_handle_update_team', 'handle_update_team');
add_action('wp_ajax_nopriv_handle_update_team', 'handle_update_team'); // for non-logged-in users
add_action('wp_ajax_user_logged_in', 'user_logged_in');
add_action('wp_ajax_nopriv_user_logged_in', 'user_logged_in');
add_action('wp_ajax_handle_results_for_team', 'handle_results_for_team');
add_action('wp_ajax_nopriv_handle_results_for_team', 'handle_results_for_team'); // for non-logged-in users
add_action('wp_ajax_handle_coach_teams_in_tournament', 'handle_coach_teams_in_tournament');
add_action('wp_ajax_nopriv_handle_coach_teams_in_tournament', 'handle_coach_teams_in_tournament'); // for non-logged-in users
add_action('wp_ajax_handle_join_team_form', 'handle_join_team_form');
add_action('wp_ajax_nopriv_handle_join_team_form', 'handle_join_team_form'); // for non-logged-in users
add_action('wp_ajax_fetch_teams_by_coach', 'fetch_teams_by_coach');
add_action('wp_ajax_nopriv_fetch_teams_by_coach', 'fetch_teams_by_coach'); // for non-logged-in users
add_action('wp_ajax_handle_delete_player', 'handle_delete_player');
add_action('wp_ajax_nopriv_handle_delete_player', 'handle_delete_player'); // for non-logged-in users
add_action('wp_ajax_handle_update_player', 'handle_update_player');
add_action('wp_ajax_nopriv_handle_update_player', 'handle_update_player'); // for non-logged-in users
add_action('wp_ajax_handle_fetch_player_info', 'handle_fetch_player_info');
add_action('wp_ajax_nopriv_handle_fetch_player_info', 'handle_fetch_player_info'); // for non-logged-in users
add_action('wp_ajax_handle_back_button', 'handle_back_button');
add_action('wp_ajax_nopriv_handle_back_button', 'handle_back_button'); // for non-logged-in users
add_action('wp_ajax_handle_add_player', 'handle_add_player');
add_action('wp_ajax_nopriv_handle_add_player', 'handle_add_player'); // for non-logged-in users
add_action('wp_ajax_handle_add_player_screen', 'handle_add_player_screen');
add_action('wp_ajax_nopriv_handle_add_player_screen', 'handle_add_player_screen'); // for non-logged-in users
add_action('wp_ajax_handle_user_logged_in', 'handle_user_logged_in');
add_action('wp_ajax_nopriv_handle_user_logged_in', 'handle_user_logged_in'); // for non-logged-in users
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
