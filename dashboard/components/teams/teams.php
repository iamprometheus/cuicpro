<?php

function create_divisions_dropdown($selected_id = null) {
	$html = "";
	$divisions = DivisionsDatabase::get_divisions();

	$html .= "<option value='0'>Division Pendiente</option>";
	foreach ($divisions as $division) {
		$category = $division->division_category === "1" ? "Varonil" : ($division->division_category === "2" ? "Femenil" : "Mixto");
		$mode = $division->division_mode === "1" ? "5v5" : "7v7";
		$html .= "<option value='" . esc_attr($division->division_id) . "'" . ($selected_id == $division->division_id ? "selected" : "") . ">" . esc_html($division->division_name) . " " . esc_html($mode) . " " . esc_html($category) . "</option>";
	}
	return $html;
}

function create_input_team() {
	$html = "";
  // dynamic input fields for adding teams
  $html .= "<div>";
  $html .= "<div id='tournament-input-container' style='text-align: center; margin-bottom: 15px; font-size: 20px;'>
              <span style='font-weight: bold; '>Registro de equipos</span>
            </div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Nombre: </span>
							<div class='table-input-cell'>
								<input type='text' id='team-name-table' placeholder='Nombre'>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Modalidad: </span>
							<div class='table-input-cell'>
								<select id='team-mode-table'>
									<option value='1'>5v5</option>
									<option value='2'>7v7</option>
								</select>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Categoria: </span>
							<div class='table-input-cell'>
								<select id='team-category-table'>
									<option value='1'>Varonil</option>
									<option value='2'>Femenil</option>
									<option value='3'>Mixto</option>
								</select>
							</div>
            </div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Logo: </span>
							<div class='table-input-cell'>	
								<input type='file' id='team-logo-input' accept='.png, .jpg, .jpeg' placeholder='Seleccionar Logo'>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
						<span class='table-cell'>Acciones: </span>
							<div class='table-input-cell'>
								<button id='add-team-button'>Agregar</button>
								<button id='update-team-button' data-team-id='0' data-team-coach-id='0' class='hidden'>Actualizar</button>
								<button id='cancel-team-button' class='hidden'>Cancelar</button>
							</div>
						</div>";
	$html .= "<div class='table-input-row'>
							<span class='table-cell'>Resultado: </span>				
							<span class='table-input-cell' id='team-result-table'>Resultado de la accion.</span>
						</div>";
  $html .= "</div>";
	

  return $html;
}

// function to display the dropdown of divisions to show teams
function cuicpro_teams_by_division($tournament) {
	$html = "<div style='margin-bottom: 15px; font-size: 20px;'>
							<span style='font-weight: bold; '>Equipos por Division de torneo seleccionado:</span>
						</div>";
	$html .= "<div class='table-view'>";
	$html .= "<select id='divisions-dropdown-tv'>\n";
  $html .= "<option value='0'>Selecciona una Division</option>\n";
	
  if (is_null($tournament)) {
		$html .= "</select>\n";
		$html .= "<div id='division-data'></div>";
		$html .= "</div>";
    return $html;
  }

  // Fetch divisions from database
  $divisions = DivisionsDatabase::get_divisions_by_tournament($tournament->tournament_id);

  if ($divisions) {
      foreach ($divisions as $division) {
				$division->division_category = $division->division_category === "1" ? "Varonil" : ($division->division_category === "2" ? "Femenil" : "Mixto");
				$division->division_mode = $division->division_mode === "1" ? "5v5" : "7v7";
          $html .= "<option value='" . esc_attr($division->division_id) . "'>" 
                      . esc_html($division->division_name) . " " . esc_html($division->division_mode) . " " . esc_html($division->division_category) . "</option>\n";
      }
  } else {
      $html .= "<option value='0'>Sin Divisiones Disponibles</option>\n";
  }

  $html .= "</select>\n";
  $html .= "<div id='division-data'></div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_players_by_team() {
	$html = "<div class='table-view'>";
	$html .= "<span>Jugadores por Equipo</span>";
	$html .= "<select id='team-players-dropdown'>\n";
	$html .= "<option value='0'>Selecciona un Equipo</option>\n";

	$html .= "</select>\n";
	$html .= "<div id='players-data'></div>";
	$html .= "</div>";

	return $html;
}

function cuicpro_teams_by_coach($tournament) {
	
	$html = "<div style='margin-bottom: 15px; font-size: 20px;'>
							<span style='font-weight: bold; '>Equipos en este torneo</span>
						</div>";
	$html .= "<div class='table-view'>";
	$html .= "<span>Equipos por Entrenador</span>";
	$html .= "<select id='coaches-dropdown-tv'>\n";
	$html .= "<option value='0'>Selecciona un Entrenador</option>\n";

	if (is_null($tournament)) {
		$html .= "</select>\n";
		$html .= "<div id='coach-data'></div>";
		$html .= "</div>";
		return $html;
	}
	// Fetch coaches from database
	$coaches = CoachesDatabase::get_coaches_by_tournament($tournament->tournament_id);

	if ($coaches) {
		foreach ($coaches as $coach) {
			$html .= "<option value='" . esc_attr($coach->coach_id) . "'>" 
						. esc_html($coach->coach_name) . "</option>\n";
		}
	} else {
		$html .= "<option value='0'>Sin Entrenadores Disponibles</option>\n";
	}

	$html .= "</select>\n";
	$html .= "<div id='coach-data'></div>";
	$html .= "</div>";

	return $html;
}

function cuicpro_teams_viewer() {
	$tournaments = TournamentsDatabase::get_active_tournaments();
	$tournament = null;
	if (!empty($tournaments)) {
		$tournament = $tournaments[0];
	}
	
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
	$html .= "<div class='table-view-container'>";
	$html .= create_input_team();
	$html .= "<div id='teams-data'>";
	$html .= cuicpro_teams_by_coach($tournament);
	$html .= cuicpro_players_by_team();
	$html .= "</div>";
	$html .= "</div>";
	$html .= "</div>";
	echo $html;
}

function cuicpro_teams_by_division_viewer() {
	$tournaments = TournamentsDatabase::get_active_tournaments();
	$tournament = null;
	if (!empty($tournaments)) {
		$tournament = $tournaments[0];
	}
	
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
	$html .= "<div class='table-view-container'>";
	$html .= "<div id='teams-data-by-division'>";
	$html .= cuicpro_teams_by_division($tournament);
	$html .= "</div>";
	$html .= "</div>";
	$html .= "</div>";
	echo $html;
}

// enqueue scripts related to this file
function enqueue_teams_scripts() {
	wp_enqueue_style( 'teams-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'teams-script',
			plugins_url('/handle_teams_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('teams-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_teams_scripts');

require_once __DIR__ . '/handle_teams_request.php';