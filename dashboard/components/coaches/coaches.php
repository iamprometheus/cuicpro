<?php

function create_input_coach() {
	$html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='table-input'>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Nombre: </span>
							<div class='table-input-cell'>
                <input type='text' id='coach-name' placeholder='Nombre'>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Contacto: </span>
              <div class='table-input-cell'>
                <input type='text' id='coach-contact' placeholder='Contacto'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Ciudad: </span>
              <div class='table-input-cell'>
                <input type='text' id='coach-city' placeholder='Ciudad'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Estado: </span>
							<div class='table-input-cell'>
                <input type='text' id='coach-state' placeholder='Estado'>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Pais: </span>
							<div class='table-input-cell'>
                <input type='text' id='coach-country' placeholder='Pais'>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
						<span class='table-cell'>Acciones: </span>
							<div class='table-input-cell'>
                <button id='add-coach-button'>Agregar</button>
								<button id='update-coach-button' data-coach-id='0' class='hidden'>Actualizar</button>
								<button id='cancel-coach-button' class='hidden'>Cancelar</button>
							</div>
						</div>";
						
	$html .= "<div class='table-input-row'>
							<span class='table-cell'>Resultado: </span>				
							<span class='table-input-cell' id='coach-result-table'>Resultado de la accion.</span>
						</div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_coaches_table(int $tournament_id) {
  $coaches = CoachesDatabase::get_coaches_by_tournament($tournament_id);

  // create table header
  $html = "<div class='table-row'>
              <span class='table-cell'>Nombre: </span>
              <span class='table-cell'>Contacto: </span>
              <span class='table-cell'>Ciudad: </span>
              <span class='table-cell'>Estado: </span>
              <span class='table-cell'>Pais: </span>
              <span class='table-cell'>Acciones: </span>
            </div>
            ";

  // add team data to table
  foreach ($coaches as $coach) {
    $html .= "<div class='table-row' id='coach-$coach->coach_id'>";
    $html .= "<span class='table-cell'>" . esc_html($coach->coach_name) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($coach->coach_contact) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($coach->coach_city) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($coach->coach_state) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($coach->coach_country) . "</span>";
    $html .= "<div class='table-cell'>
                <button id='edit-coach-button' data-coach-id=$coach->coach_id>Editar</button>
                <button id='delete-coach-button' data-coach-id=$coach->coach_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  return $html;
}

function cuicpro_coach_viewer() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }
  
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
  $html .= "<div class='table-view-container'>";
  $html .= create_input_coach();
  $html .= "<div id='coaches-data'>";
  $html .= cuicpro_coaches_table($tournament->tournament_id);
  $html .= "</div>";
  $html .= "</div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_coach_scripts() {
	wp_enqueue_style( 'coaches-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'coach-script',
			plugins_url('/handle_coaches_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('coach-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_coach_scripts');

require_once __DIR__ . '/handle_coaches_request.php';