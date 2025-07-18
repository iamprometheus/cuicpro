<?php


function create_division_entry_selector($selected, $division) {
  $mode = ModesDatabase::get_mode_by_id($division->division_mode);
  $category = CategoriesDatabase::get_category_by_id($division->division_category);
  $html = "";
  $html .= "<div class='tournament-item' id='division-" . esc_attr($division->division_id) . "' $selected>";
  $html .= "<span class='tournament-item-name'>" . esc_html($division->division_name) . " " . $mode->mode_description . " " . $category->category_description . "</span>";
  $html .= "</div>";
  return $html;
}

function create_division_list_selector($divisions) {
  $html = "<div class='tournaments-list-container' id='divisions-selector-register'>";
  if (empty($divisions)) {
    $html .= "<div class='tournament-item-header'>";
    $html .= "<span class='tournament-item-name'>No hay divisiones activas para el torneo seleccionado</span>";
    $html .= "</div>";
  } else {
    $html .= "<div class='tournament-item-header'>";
    $html .= "<span class='tournament-item-name'>Divisiones:</span>";
    $html .= "</div>";
    foreach ($divisions as $index => $division) {
      $selected = $index === 0 ? "selected" : "";
      $html .= create_division_entry_selector($selected, $division);
    }
  }
  $html .= "</div>";
  return $html;
}

function render_pending_teams_from_register($tournament_id, $division_id) {
  if ($tournament_id === null || $division_id === null) {
    $html = "";
    $html .= "<table id='registered-teams' border='1' align='center'>";
    $html .= "<caption>No hay equipos en la fila de registro</caption>";
    $html .= "</table>";
    return $html;
  }

  $teams = TeamRegisterQueueDatabase::get_teams_by_tournament_and_division($tournament_id, $division_id);
  $html = "";
  $html .= "<table id='pending-teams' border='1' align='center'>";
  if (empty($teams)) {
    $html .= "<caption>No hay equipos en la fila de registro</caption>";
    $html .= "</table>";
    return $html;
  }
  $html .= "<caption>Equipos en la fila de registro:</caption>";
  $html .= "<thead>";
  $html .= "<tr>";
  $html .= "<th>Nombre del Coach</th>";
  $html .= "<th>Contacto</th>";
  $html .= "<th>Nombre del Equipo</th>";
  $html .= "<th>Ubicacion</th>";
  $html .= "<th>Logo</th>";
  $html .= "<th>Acciones</th>";
  $html .= "</tr>";
  $html .= "</thead>";
  $html .= "<tbody>";
  foreach ($teams as $team) {
    $coach = CoachesUserDatabase::get_coach_by_id($team->coach_id);
    $team_user = TeamsUserDatabase::get_team_by_id($team->team_id);

    $html .= "<tr>";
    $html .= "<td>" . esc_html($coach->user_name) . "</td>";
    $html .= "<td>" . esc_html($coach->user_contact) . "</td>";
    $html .= "<td>" . esc_html($team_user->team_name) . "</td>";
    $html .= "<td>" . esc_html($coach->user_city) . ", " . esc_html($coach->user_state) . ", " . esc_html($coach->user_country) . "</td>";
    $html .= "<td><img src='" . wp_get_attachment_image_url($team_user->team_logo, 'full') . "' alt='Logo' /></td>";
    $html .= "<td>
                <div>
                  <button id='accept-team-button' data-record-id='" . esc_attr($team->team_register_queue_id) . "'>Aceptar</button>
                  <button id='reject-team-button' data-record-id='" . esc_attr($team->team_register_queue_id) . "'>Rechazar</button>
                </div>
              </td>";
    $html .= "</tr>";
  }
  $html .= "</tbody>";
  $html .= "</table>";
  return $html;
}

function render_registered_teams_table($division_id) {
  if ($division_id === null) {
    $html = "";
    $html .= "<table id='registered-teams' border='1' align='center'>";
    $html .= "<caption>No hay equipos registrados</caption>";
    $html .= "</table>";
    return $html;
  }
  $teams = TeamsDatabase::get_teams_by_division($division_id);
  $html = "";
  $html .= "<table id='registered-teams' border='1' align='center'>";
  if (empty($teams)) {
    $html .= "<caption>No hay equipos registrados</caption>";
    $html .= "</table>";
    return $html;
  }
  $html .= "<caption>Equipos registrados:</caption>";
  $html .= "<thead>";
  $html .= "<tr>";
  $html .= "<th>Nombre del Coach</th>";
  $html .= "<th>Nombre del Equipo</th>";
  $html .= "<th>Ubicacion</th>";
  $html .= "<th>Logo</th>";
  $html .= "</tr>";
  $html .= "</thead>";
  $html .= "<tbody>";
  foreach ($teams as $team) {
    $coach = CoachesDatabase::get_coach_by_id($team->coach_id);

    $html .= "<tr>";
    $html .= "<td>" . esc_html($coach->coach_name) . "</td>";
    $html .= "<td>" . esc_html($team->team_name) . "</td>";
    $html .= "<td>" . esc_html($coach->coach_city) . ", " . esc_html($coach->coach_state) . ", " . esc_html($coach->coach_country) . "</td>";
    $html .= "<td><img src='" . wp_get_attachment_image_url($team->logo, 'full') . "' alt='Logo' /></td>";
    $html .= "</tr>";
  }
  $html .= "</tbody>";
  
  $html .= "</table>";
  return $html;
}

function register_viewer() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  $divisions = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
    $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament->tournament_id);
  }

  $html = "";
  $html .= "<div id='division-selector-register'>";
  $html .= create_division_list_selector($divisions);
  $html .= "</div>";
  echo $html;
}

// enqueue scripts related to this file
function enqueue_register_scripts() {
	wp_enqueue_style( 'register-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'register-script',
			plugins_url('/handle_register_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('register-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_register_scripts');

require_once __DIR__ . '/handle_register_request.php';