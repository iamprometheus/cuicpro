<?php

function render_teams() {
  $teams = TeamsDatabase::get_teams();
  $html = "";

  $html .= "<option value='0'>Ninguno</option>";
  foreach ($teams as $team) {
    $html .= "<option value='" . $team->team_id . "'>" . $team->team_name . "</option>";
  }
  return $html;
}

function create_input_official() {
  $tournament = TournamentsDatabase::get_active_tournament();
  
  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='official-input-wrapper' id='dynamic-input-official'>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Nombre: </span>
              <div class='official-input-cell'>
                <input type='text' id='official-name' placeholder='Nombre'>
              </div>
            </div>";
            
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Horas: </span>
              <div class='official-input-cell'>
                <input type='number' min='1' max='12' id='official-hours' placeholder='Horas' required>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Dias: </span>
              <div class='official-input-cell'>
                <input type='text' id='official-schedule' readonly value='$tournament->tournament_days'>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Modo: </span>
              <div class='official-input-cell'>
                <select id='official-mode'>
                  <option value='1'>5v5</option>
                  <option value='2'>7v7</option>
                  <option value='3'>Ambos</option>
                </select>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Equipo: </span>
              <div class='official-input-cell'>
                <select id='official-team-id'>
                  " . render_teams() . "
                </select>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Ciudad: </span>
              <div class='official-input-cell'>
                <input type='text' id='official-city' placeholder='Ciudad'>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Estado: </span>
              <div class='official-input-cell'>
                <input type='text' id='official-state' placeholder='Estado'>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Pais: </span>
              <div class='official-input-cell'>
                <input type='text' id='official-country' placeholder='Pais'>
              </div>
            </div>";
  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Activo: </span>
              <div class='official-input-cell'>
                <input type='checkbox' value='1' checked id='official-active' disabled>
              </div>
            </div>";

  $html .= "<div class='official-input-row'>
              <span class='official-cell-header'>Acciones: </span>
              <div class='official-input-cell'>
                <button id='add-official-button'>Agregar</button>
              </div>
            </div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_official_viewer() {
  $officials = OfficialsDatabase::get_officials();

  $html = "<div class='officials-wrapper'> ";
  $html .= create_input_official();
  // create table header
  $html .= "<div class='officials-table'>
              <div class='officials-table-row'>
                <span class='officials-table-cell'>Nombre: </span>
                <span class='officials-table-cell'>Horas: </span>
                <span class='officials-table-cell'>Dias: </span>
                <span class='officials-table-cell'>Modo: </span>
                <span class='officials-table-cell'>Equipo: </span>
                <span class='officials-table-cell'>Ciudad: </span>
                <span class='officials-table-cell'>Estado: </span>
                <span class='officials-table-cell'>Pais: </span>
                <span class='officials-table-cell'>Activo: </span>
                <span class='officials-table-cell'>Acciones: </span>
              </div>
            ";

  // add team data to table
  foreach ($officials as $official) {
    $checked = $official->official_is_active ? 'checked' : '';
    $team = "";
    if ( !$official->official_team_id ) {
      $team = "Ninguno";
    } else {
      $team = TeamsDatabase::get_team_by_id($official->official_team_id)->team_name;
    }
    $html .= "<div class='officials-table-row' id='official-$official->official_id'>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_name) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_hours) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_schedule) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_mode) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($team) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_city) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_state) . "</span>";
    $html .= "<span class='officials-table-cell'>" . esc_html($official->official_country) . "</span>";
    $html .= "<div class='officials-table-cell'>
                <input type='checkbox' id='delete-official-button' $checked>
              </div>";
    $html .= "<div class='officials-table-cell'>
                <button id='edit-official-button' data-official-id=$official->official_id>Editar</button>
                <button id='delete-official-button' data-official-id=$official->official_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  $html .= "</div> </div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_official_scripts() {
	wp_enqueue_style( 'officials-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'official-script',
			plugins_url('/handle_officials_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('official-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_official_scripts');

require_once __DIR__ . '/handle_officials_request.php';