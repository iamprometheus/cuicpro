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
  $html .= "<div class='table-input' id='dynamic-input-official'>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Nombre: </span>
              <div class='table-input-cell'>
                <input type='text' id='official-name' placeholder='Nombre'>
              </div>
            </div>";
            
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Horas: </span>
              <div class='table-input-cell'>
                <input type='number' min='1' max='12' id='official-hours' placeholder='Horas' required>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Dias: </span>
              <div class='table-input-cell'>
                <input type='text' id='official-schedule' readonly value='$tournament->tournament_days'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Modo: </span>
              <div class='table-input-cell'>
                <select id='official-mode'>
                  <option value='1'>5v5</option>
                  <option value='2'>7v7</option>
                  <option value='3'>Ambos</option>
                </select>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Equipo: </span>
              <div class='table-input-cell'>
                <select id='official-team-id'>
                  " . render_teams() . "
                </select>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Ciudad: </span>
              <div class='table-input-cell'>
                <input type='text' id='official-city' placeholder='Ciudad'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Estado: </span>
              <div class='table-input-cell'>
                <input type='text' id='official-state' placeholder='Estado'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Pais: </span>
              <div class='table-input-cell'>
                <input type='text' id='official-country' placeholder='Pais'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Activo: </span>
              <div class='table-input-cell'>
                <input type='checkbox' value='1' checked id='official-active' disabled>
              </div>
            </div>";

  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Acciones: </span>
              <div class='table-input-cell'>
                <button id='add-official-button'>Agregar</button>
              </div>
            </div>";
  
	$html .= "<div class='table-input-row'>
              <span class='table-cell'>Resultado: </span>				
              <span class='table-input-cell' id='official-result-table'>Resultado de la accion.</span>
            </div>";
  
  $html .= "</div>";

  return $html;
}

function render_officials() {

  $officials = OfficialsDatabase::get_officials();

  $html = "<div class='table-row'>
            <span class='table-cell'>Nombre: </span>
            <span class='table-cell'>Horas: </span>
            <span class='table-cell'>Dias: </span>
            <span class='table-cell'>Modo: </span>
            <span class='table-cell'>Equipo: </span>
            <span class='table-cell'>Ciudad: </span>
            <span class='table-cell'>Estado: </span>
            <span class='table-cell'>Pais: </span>
            <span class='table-cell'>Activo: </span>
            <span class='table-cell'>Acciones: </span>
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
    $html .= "<div class='table-row' id='official-$official->official_id'>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_name) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_hours) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_schedule) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_mode) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($team) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_city) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_state) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_country) . "</span>";
    $html .= "<div class='table-cell'>
                <input type='checkbox' id='delete-official-button' $checked>
              </div>";
    $html .= "<div class='table-cell'>
                <button id='edit-official-button' data-official-id=$official->official_id>Editar</button>
                <button id='delete-official-button' data-official-id=$official->official_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  return $html;
}

function cuicpro_official_viewer() {

  $html = "<div class='table-view-container'> ";
  $html .= create_input_official();
  // create table header

  $html .= "<div id='officials-data'>";
  $html .= render_officials();
  $html .= "</div>";
  $html .= "</div>";

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