<?php

function create_dynamic_input_coach() {
  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='coach-wrapper' id='dynamic-input-coach'>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-name-cv' placeholder='Nombre'>
            </div>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-contact-cv' placeholder='Contacto'>
            </div>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-city-cv' placeholder='Ciudad'>
            </div>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-state-cv' placeholder='Estado'>
            </div>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-country-cv' placeholder='Pais'>
            </div>";
  $html .= "<div class='coach-cell'>
              <button id='add-coach-button-cv'>Agregar</button>
            </div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_coach_viewer() {
  $coaches = CoachesDatabase::get_coaches();

  // create table header
  $html = "<div class='coaches-wrapper'>
            <div class='coaches-header'>
              <span class='coach-cell'>Nombre: </span>
              <span class='coach-cell'>Contacto: </span>
              <span class='coach-cell'>Ciudad: </span>
              <span class='coach-cell'>Estado: </span>
              <span class='coach-cell'>Pais: </span>
              <span class='coach-cell'>Acciones: </span>
            </div>
            ";

  // add team data to table
  foreach ($coaches as $coach) {
    $html .= "<div class='coach-wrapper' id='coach-$coach->coach_id'>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_name) . "</span>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_contact) . "</span>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_city) . "</span>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_state) . "</span>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_country) . "</span>";
    $html .= "<div class='coach-cell'>
                <button id='delete-coach-button' data-coach-id=$coach->coach_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  $html .= create_dynamic_input_coach();
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