<?php

function create_divisions_dropdown_for_teams($selected_id = null, $team_category, $team_mode, $tournament_id) {
	$html = "";
	$divisions = DivisionsDatabase::get_divisions_by_tournament($tournament_id);

	$html .= "<option value='0'>Division Pendiente</option>";
	foreach ($divisions as $division) {
    if ($division->division_category !== $team_category || $division->division_mode !== $team_mode) {
      continue;
    }
		$category = $division->division_category === "1" ? "Varonil" : ($division->division_category === "2" ? "Femenil" : "Mixto");
		$mode = $division->division_mode === "1" ? "5v5" : "7v7";
		$html .= "<option value='" . esc_attr($division->division_id) . "'" . ($selected_id == $division->division_id ? "selected" : "") . ">" . esc_html($division->division_name) . " " . esc_html($mode) . " " . esc_html($category) . "</option>";
	}
	return $html;
}

function on_add_team($team) {
  $team_category = $team->team_category === "1" ? "Varonil" : ($team->team_category === "2" ? "Femenino" : "Mixto");
  $team_mode = $team->team_mode === "1" ? "5v5" : "7v7";
  $html = "";
  $html .= "<div class='table-row' id='team-$team->team_id'>";
  $html .= "<span class='table-cell'>" . esc_html($team->team_name) . "</span>";
  $html .= "<div class='table-cell'>
              <select id='team-division-dropdown' data-team-id=$team->team_id>
                " . create_divisions_dropdown_for_teams($team->division_id, $team->team_category, $team->team_mode, $team->tournament_id) . "
              </select>
            </div>";
  $html .= "<span class='table-cell'>" . esc_html($team_category) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($team_mode) . "</span>";
  
  $html .= "<div class='table-cell'>
              <input type='checkbox' id='enrolled-team-button' data-team-id=$team->team_id>
            </div>";
  $html .= "<div class='table-cell'>
              <img src='" . wp_get_attachment_image_url($team->logo, 'full') . "'>
            </div>";
  $html .= "<div class='table-cell'>
              <button id='edit-team-button' data-team-id=$team->team_id>Editar</button>
              <button id='delete-team-button' data-team-id=$team->team_id>Eliminar</button>
            </div>";
  $html .= "</div>";
  return $html;
}

function fetch_division_data() {
  if (!isset($_POST['division_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener los equipos']);
  }
  $division_id = intval($_POST['division_id']);
  $teams = TeamsDatabase::get_teams_by_division($division_id);

  // create table header
  $html = "<div class='table-wrapper'>
            <div class='table-row'>
              <span class='table-cell'>Equipo: </span>
              <span class='table-cell'>Entrenador: </span>
              <span class='table-cell'>Logo: </span>
            </div>
            ";

  // add team data to table
  foreach ($teams as $team) {
    $coach_name = CoachesDatabase::get_coach_by_id($team->coach_id)->coach_name;
    $html .= "<div class='table-row' id='team-$team->team_id'>";
    $html .= "<span class='table-cell'>" . esc_html($team->team_name) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($coach_name) . "</span>";
    $html .= "<div class='table-cell'>
                <img src='" . wp_get_attachment_image_url($team->logo, 'full') . "'>
              </div>";
    $html .= "</div>";
  }

  wp_send_json_success(['html' => $html]);
}

function fetch_team_players_data() {
  if (!isset($_POST['team_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener los jugadores']);
  }
  $team_id = intval($_POST['team_id']);
  $players = PlayersDatabase::get_players_by_team($team_id);

  // create table header
  $html = "<div class='table-wrapper'>
            <div class='table-row'>
              <span class='table-cell'>Jugador: </span>
              <span class='table-cell'>Foto: </span>
            </div>
            ";

  // add player data to table
  foreach ($players as $player) {
    $html .= "<div class='table-row' id='player-$player->player_id'>";
    $html .= "<span class='table-cell'>" . esc_html($player->player_name) . "</span>";
    $html .= "<span class='table-cell'>" . "<img src='" . wp_get_attachment_image_url($player->player_photo, 'full') . "'>" . "</span>";
    $html .= "</div>";
  }

  wp_send_json_success(['html' => $html]);
}

function create_team_entry($team) {
  $html = "";

  $team_category = $team->team_category === "1" ? "Varonil" : ($team->team_category === "2" ? "Femenil" : "Mixto");
  $team_mode = $team->team_mode === "1" ? "5v5" : "7v7";
  $checked_enrolled = $team->is_enrolled ? 'checked' : '';

  $html .= "<div class='table-row' id='team-$team->team_id'>";
  $html .= "<span class='table-cell'>" . esc_html($team->team_name) . "</span>";
  $html .= "<div class='table-cell'>
              <select id='team-division-dropdown' data-team-id=$team->team_id>
                " . create_divisions_dropdown_for_teams($team->division_id, $team->team_category, $team->team_mode, $team->tournament_id) . "
              </select>
            </div>";
  $html .= "<span class='table-cell'>" . esc_html($team_category) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($team_mode) . "</span>";
  $html .= "<div class='table-cell'>
              <input type='checkbox' id='enrolled-team-button' data-team-id=$team->team_id $checked_enrolled>
            </div>";
  $html .= "<div class='table-cell'>
              <img src='" . wp_get_attachment_image_url($team->logo, 'full') . "'>
            </div>";
  $html .= "<div class='table-cell'>
              <button id='edit-team-button' data-team-id=$team->team_id>Editar</button>
              <button id='delete-team-button' data-team-id=$team->team_id>Eliminar</button>
            </div>";
  $html .= "</div>";

  return $html;
}

function create_coach_data($coach_id) {
  $teams = TeamsDatabase::get_teams_by_coach($coach_id);

  // create table header
  $html = "<div class='table-wrapper' id='teams-coach-data'>
            <div class='table-row'>
              <span class='table-cell'>Equipo: </span>
              <span class='table-cell'>Division: </span>
              <span class='table-cell'>Categoria: </span>
              <span class='table-cell'>Modalidad: </span>
              <span class='table-cell'>Inscrito: </span>
              <span class='table-cell'>Logo: </span>
              <span class='table-cell'>Acciones: </span>
            </div>
            ";

          
  // add team data to table
  foreach ($teams as $team) {
    $html .= create_team_entry($team);
  }

  return $html;
}

function create_players_data($coach_id) {
  // create table header
  $html = "<div class='table-wrapper' id='players-coach-data'>
            <div class='table-row'>
              <span class='table-cell'>Jugador: </span>
              <span class='table-cell'>Foto: </span>
            </div>
            ";

  $teams = TeamsDatabase::get_teams_by_coach($coach_id);
  if (empty($teams))  return $html;
  $players = PlayersDatabase::get_players_by_team($teams[0]->teams_team_id);
    // add players data to table
  foreach ($players as $player) {
    $html .= "<div class='table-row' id='player-$player->player_id'>";
    $html .= "<span class='table-cell'>" . esc_html($player->player_name) . "</span>";
    $html .= "<span class='table-cell'>" . "<img src='" . wp_get_attachment_image_url($player->player_photo, 'full') . "'>" . "</span>";
    $html .= "</div>";
  }

  return $html;
}

function create_team_players_dropdown($coach_id) {
  $html = "<select id='team-players-dropdown'>\n";

  $teams = TeamsDatabase::get_teams_by_coach($coach_id);

  if ($teams) {
    foreach ($teams as $team) {
      $html .= "<option value='" . esc_attr($team->team_id) . "'>" 
                  . esc_html($team->team_name) . "</option>\n";
    }
  } else {
    $html .= "<option value='0'>No hay equipos registrados</option>\n";
  }

  $html .= "</select>\n";
  return $html;
}

function fetch_coach_data() {
  if (!isset($_POST['coach_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener los equipos']);
  }
  $coach_id = intval($_POST['coach_id']);
  
  $html = create_coach_data($coach_id);
  $team_players_dropdown = create_team_players_dropdown($coach_id);
  $players = create_players_data($coach_id);

  wp_send_json_success(['message' => 'Equipos obtenidos correctamente', 'html' => $html, 'players' => $players, 'team_players_dropdown' => $team_players_dropdown]);
}

function edit_team() {
  if (!isset($_POST['team_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener el equipo']);
  }

  $team_id = intval($_POST['team_id']);

  $team = TeamsDatabase::get_team_by_id($team_id);
    wp_send_json_success(['message' => 'Editando equipo.', 'team' =>$team]);
}

function delete_team() {
  if (!isset($_POST['team_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el equipo']);
  }
  $team_id = intval($_POST['team_id']);
  $team = TeamsDatabase::get_team_by_id($team_id);
  $tournament_id = $team->tournament_id;
  $tournament_started = TournamentsDatabase::is_tournament_started($tournament_id);

  if ($tournament_started) {
    wp_send_json_error(['message' => 'No se pudo eliminar el equipo, el torneo ha comenzado']);
  }

  TeamsDatabase::delete_team($team_id);
  TeamsUserDatabase::update_team_is_registered($team->teams_team_id, false);
  wp_send_json_success(['message' => 'Equipo eliminado correctamente']);
}

function update_team() {
  if (!isset($_POST['team_id']) || !isset($_POST['team_name']) || !isset($_POST['team_category']) || !isset($_POST['team_mode']) || !isset($_POST['coach_id']) || !isset($_POST['logo'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $team_id = intval($_POST['team_id']);
  $team_name = sanitize_text_field($_POST['team_name']);
  $coach_id = intval($_POST['coach_id']);
  $team_category = intval($_POST['team_category']);
  $team_mode = intval($_POST['team_mode']);
  $logo = sanitize_text_field($_POST['logo']);
  if ($logo === "") {
    $team_logo = TeamsDatabase::get_team_by_id($team_id)->logo;
    $logo = $team_logo;
  }
  $visible = true;

  $result = TeamsDatabase::update_team($team_id, $team_name, $team_category, $team_mode, $coach_id, $logo, $visible);
  if ($result) {
    $team = TeamsDatabase::get_team_by_id($team_id);
    if ($team->team_mode != $team_mode || $team->team_category != $team_category) {
      TeamsDatabase::update_team_division($team_id, null);
    }
    wp_send_json_success(['message' => 'Equipo actualizado correctamente', 'coachData' => create_coach_data($team->coach_id, $team->tournament_id), 'coachID' => $team->coach_id]);
  }
  wp_send_json_error(['message' => 'Equipo no actualizado, equipo ya existe']);
}

function update_team_division() {
  if (!isset($_POST['team_id']) || !isset($_POST['division_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $team_id = intval($_POST['team_id']);
  $division_id = intval($_POST['division_id']);

  if ($division_id == 0) {
    $division_id = null;
  }

  $result = TeamsDatabase::update_team_division($team_id, $division_id);
  if ($result) {
    wp_send_json_success(['message' => 'Division actualizada correctamente.']);
  }
  wp_send_json_error(['message' => 'No se pudo actualizar la division.']);
}

function update_team_enrolled() {
  if (!isset($_POST['team_id']) || !isset($_POST['team_is_enrolled'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $team_id = intval($_POST['team_id']);
  $team_is_enrolled = intval($_POST['team_is_enrolled']);

  $result = TeamsDatabase::update_team_enrolled($team_id, $team_is_enrolled);
  if ($result) {
    if ($team_is_enrolled == 1) {
      wp_send_json_success(['message' => 'Equipo inscrito correctamente.']);
    } else {
      wp_send_json_success(['message' => 'Equipo desinscrito correctamente.']);
    }
  }
  wp_send_json_error(['message' => 'No se pudo inscribir el equipo.']);
}

function addImageToWordPressMediaLibrary(string $path_to_file, string $image, string $title, &$attachment_id = null): bool { 
  $file_array = ["name" => $image, "tmp_name" => $path_to_file, "title" => $title]; // Add image to Media Library 
  $attachment_id = media_handle_sideload($file_array , 0, ''); 
  if(!is_numeric($attachment_id )){ 
    return false;
  } 
  return true; 
}

function add_team() {
  if (!isset($_POST['tournament_id']) || !isset($_POST['division_id']) || !isset($_POST['team_name']) || !isset($_POST['team_category']) || !isset($_POST['team_mode']) || !isset($_POST['coach_id']) || !isset($_FILES['logo'])) {
    wp_send_json_error(['message' => 'Faltan datos', 'data' => $_POST]);
  }
  $team_name = sanitize_text_field($_POST['team_name']);
  $division_id = intval($_POST['division_id']);
  if ($division_id == 0) {
    $division_id = null;
  }
  $team_category = intval($_POST['team_category']);
  $team_mode = intval($_POST['team_mode']);
  $coach_id = intval($_POST['coach_id']);
  $tournament_id = intval($_POST['tournament_id']);
  $logo = $_FILES['logo'];
  
  // upload image to wordpress and get the attachment id to link to team
  $attachment_id = null;
  addImageToWordPressMediaLibrary($logo['tmp_name'], $logo['name'], $logo['name'], $attachment_id);
  
  $result = TeamsDatabase::insert_team($tournament_id, $team_name, $division_id, $team_category, $team_mode, $coach_id, strval($attachment_id), null);
  if ($result[0]) {
    $team = TeamsDatabase::get_team_by_id($result[1]);
    wp_send_json_success(['message' => 'Equipo agregado correctamente', 'html' => on_add_team($team)]);
  }
  wp_send_json_error(['message' => 'Equipo no agregado, equipo ya existe']);
}

function add_teams_bulk() {
    if (!isset($_POST['team_name']) || !isset($_POST['team_category']) || !isset($_POST['team_mode']) || !isset($_POST['coach_name']) || !isset($_POST['tournament_id'])) {
      wp_send_json_error(['message' => 'Faltan datos']);
    }
    $team_name = sanitize_text_field($_POST['team_name']);
    $coach_name = sanitize_text_field($_POST['coach_name']);
  
    $coach_id = CoachesDatabase::get_coach_by_name(null, $coach_name)->coach_id;
    $team_category = intval($_POST['team_category']);
    $team_mode = intval($_POST['team_mode']);
    $tournament_id = intval($_POST['tournament_id']);
    
    $result = TeamsDatabase::insert_team($tournament_id, $team_name, null, $team_category, $team_mode, $coach_id, $team_name, null);
    if ($result) wp_send_json_success(['message' => 'Equipo agregado correctamente']);
    wp_send_json_error(['message' => 'Equipo no agregado, equipo ya existe']);
}

add_action('wp_ajax_fetch_team_players_data', 'fetch_team_players_data');
add_action('wp_ajax_nopriv_fetch_team_players_data', 'fetch_team_players_data');
add_action('wp_ajax_update_team_enrolled', 'update_team_enrolled');
add_action('wp_ajax_nopriv_update_team_enrolled', 'update_team_enrolled');
add_action('wp_ajax_edit_team', 'edit_team');
add_action('wp_ajax_nopriv_edit_team', 'edit_team');
add_action('wp_ajax_update_team', 'update_team');
add_action('wp_ajax_nopriv_update_team', 'update_team');
add_action('wp_ajax_update_team_division', 'update_team_division');
add_action('wp_ajax_nopriv_update_team_division', 'update_team_division');
add_action('wp_ajax_add_teams_bulk', 'add_teams_bulk');
add_action('wp_ajax_nopriv_add_teams_bulk', 'add_teams_bulk');
add_action('wp_ajax_fetch_coach_data', 'fetch_coach_data');
add_action('wp_ajax_nopriv_fetch_coach_data', 'fetch_coach_data');
add_action('wp_ajax_fetch_division_data', 'fetch_division_data');
add_action('wp_ajax_nopriv_fetch_division_data', 'fetch_division_data');
add_action('wp_ajax_delete_team', 'delete_team');
add_action('wp_ajax_nopriv_delete_team', 'delete_team');
add_action('wp_ajax_add_team', 'add_team');
add_action('wp_ajax_nopriv_add_team', 'add_team');
