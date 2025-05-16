<?php

function create_dynamic_input_coach() {
  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='coach-wrapper' id='dynamic-input-coach'>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-name-cv' placeholder='Nombre'>
            </div>";
  $html .= "<div class='coach-input-cell'>
              <select id='coach-mode-cv'>
                <option value='5v5'>5v5</option>
                <option value='7v7'>7v7</option>
                <option value='ambos'>Ambos</option>
              </select>
            </div>";
  $html .= "<div class='coach-input-cell'>
              <input type='text' id='coach-phone-cv' placeholder='Contacto'>
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
              <span class='coach-cell'>Entrenador: </span>
              <span class='coach-cell'>Modalidad: </span>
              <span class='coach-cell'>Contacto: </span>
              <span class='coach-cell'>Acciones: </span>
            </div>
            ";

  // add team data to table
  foreach ($coaches as $coach) {
    $html .= "<div class='coach-wrapper' id='coach-$coach->coach_id'>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_name) . "</span>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_mode) . "</span>";
    $html .= "<span class='coach-cell'>" . esc_html($coach->coach_phone) . "</span>";
    $html .= "<div class='coach-cell'>
                <button id='delete-coach-button-cv' data-coach-id=$coach->coach_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  $html .= create_dynamic_input_coach();
  $html .= "</div>";

  echo $html;
}


// function to register the dashboard widget
function coaches_dashboard_widgets() {
	// Register your custom WordPress admin dashboard widget
	wp_add_dashboard_widget('cuicpro_coaches_widget', 'CUICPRO Entrenadores', 'cuicpro_coach_viewer');
}

// hooks up your code to dashboard setup
add_action('wp_dashboard_setup', 'coaches_dashboard_widgets');

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