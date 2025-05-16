<?php

function create_dynamic_input_league() {
  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='league-wrapper' id='dynamic-input-league'>";
  $html .= "<div class='league-input-cell'>
              <input type='text' id='league-name-lv' placeholder='Nombre'>
            </div>";
  $html .= "<div class='league-input-cell'>
              <select id='league-mode-lv'>
                <option value='5v5'>5v5</option>
                <option value='7v7'>7v7</option>
              </select>
            </div>";
  $html .= "<div class='league-cell'>
              <button id='add-league-button-lv'>Agregar</button>
            </div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_league_viewer() {
  $leagues = LeaguesDatabase::get_leagues();

  // create table header
  $html = "<div class='leagues-wrapper'>
            <div class='leagues-header'>
              <span class='league-cell'>Categoria: </span>
              <span class='league-cell'>Modalidad: </span>
              <span class='league-cell'>Acciones: </span>
            </div>
            ";

  // add team data to table
  foreach ($leagues as $league) {
    $html .= "<div class='league-wrapper' id='league-$league->league_id'>";
    $html .= "<span class='league-cell'>" . esc_html($league->league_name) . "</span>";
    $html .= "<span class='league-cell'>" . esc_html($league->league_mode) . "</span>";
    $html .= "<div class='league-cell'>
                <button id='delete-league-button-lv' data-league-id=$league->league_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }


  $html .= create_dynamic_input_league();
  $html .= "</div>";

  echo $html;
}


// function to register the dashboard widget
function leagues_dashboard_widgets() {
	// Register your custom WordPress admin dashboard widget
	wp_add_dashboard_widget('cuicpro_leagues_widget', 'CUICPRO Categorias', 'cuicpro_league_viewer');
}

// hooks up your code to dashboard setup
add_action('wp_dashboard_setup', 'leagues_dashboard_widgets');

// enqueue scripts related to this file
function enqueue_league_scripts() {
	wp_enqueue_style( 'leagues-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'league-script',
			plugins_url('/handle_leagues_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('league-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_league_scripts');

require_once __DIR__ . '/handle_leagues_request.php';