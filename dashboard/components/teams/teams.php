<?php

function create_divisions_dropdown($selected_id = null) {
		$html = "";
		$active_tournament = TournamentsDatabase::get_active_tournament();
		$divisions = DivisionsDatabase::get_divisions($active_tournament->tournament_id);
		$html .= "<option value='0'>Division Pendiente</option>";
		foreach ($divisions as $division) {
			$html .= "<option value='" . esc_attr($division->division_id) . "'" . ($selected_id == $division->division_id ? "selected" : "") . ">" . esc_html($division->division_name) . "</option>";
		}
		return $html;
	}

function create_input_team() {
	$html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='team-input-table'>";
  $html .= "<div class='team-input-row'>
							<span class='team-input-cell-header'>Nombre: </span>
							<div class='team-input-cell'>
								<input type='text' id='team-name-table' placeholder='Nombre'>
							</div>
						</div>";
  $html .= "<div class='team-input-row'>
							<span class='team-input-cell-header'>Division: </span>
							<div class='team-input-cell'>
								<select id='team-division-table'>
									" . create_divisions_dropdown() . "
								</select>
							</div>
						</div>";	
  $html .= "<div class='team-input-row'>
							<span class='team-input-cell-header'>Modalidad: </span>
							<div class='team-input-cell'>
								<select id='team-mode-table'>
									<option value='1'>5v5</option>
									<option value='2'>7v7</option>
								</select>
							</div>
						</div>";
  $html .= "<div class='team-input-row'>
							<span class='team-input-cell-header'>Categoria: </span>
							<div class='team-input-cell'>
								<select id='team-category-table'>
									<option value='1'>Varonil</option>
									<option value='2'>Femenil</option>
									<option value='3'>Mixto</option>
								</select>
							</div>
            </div>";
  $html .= "<div class='team-input-row'>
							<span class='team-input-cell-header'>Logo: </span>
							<div class='team-input-cell'>	
								<input type='text' id='team-logo-table' placeholder='Logo'>
							</div>
						</div>";
  $html .= "<div class='team-input-row'>
						<span class='team-input-cell-header'>Acciones: </span>
							<div class='team-input-cell'>
								<button id='add-team-button'>Agregar</button>
								<button id='update-team-button' data-team-id='0' data-team-coach-id='0' class='hidden'>Actualizar</button>
								<button id='cancel-team-button' class='hidden'>Cancelar</button>
							</div>
						</div>";
						
	$html .= "<div class='team-input-row'>
							<span class='team-input-cell-header-result'>Resultado: </span>				
							<span class='team-input-cell-result' id='team-result-table'>Resultado de la accion.</span>
						</div>";
  $html .= "</div>";
	

  return $html;
}


// function to display the dropdown of divisions to show teams
function cuicpro_teams_by_division() {
  $html = "<div class='teams-view'>";
	$html .= "<select id='divisions-dropdown-tv'>\n";
  $html .= "<option value='0'>Selecciona una Division</option>\n";
	
  // Fetch divisions from database
  $active_tournament = TournamentsDatabase::get_active_tournament();
  $divisions = $active_tournament ? DivisionsDatabase::get_divisions($active_tournament->tournament_id) : null;

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

function cuicpro_teams_by_coach() {
	$html = "<div class='teams-view'>";
	$html .= "<select id='coaches-dropdown-tv'>\n";
	$html .= "<option value='0'>Selecciona un Entrenador</option>\n";

	// Fetch coaches from database
	$coaches = CoachesDatabase::get_coaches();

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
	$html = "<div class='teams-view-container'>";
	$html .= create_input_team();
	$html .= "<div id='teams-data'>";
	$html .= cuicpro_teams_by_coach();
	$html .= cuicpro_teams_by_division();
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