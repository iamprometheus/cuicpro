<?php

// function to display the dropdown of leagues to show teams
function cuicpro_teams_by_league() {
  $html = "<div class='teams-wrapper'>";
  $dropdown = "<select id='leagues-dropdown-tv'>\n";
  $dropdown .= "<option value='0'>Selecciona una Categoria</option>\n";

  // Fetch leagues from database
  $leagues = LeaguesDatabase::get_leagues();

  if ($leagues) {
      foreach ($leagues as $league) {
          $dropdown .= "<option value='" . esc_attr($league->league_id) . "'>" 
                      . esc_html($league->league_name) . " " . esc_html($league->league_mode) . "</option>\n";
      }
  } else {
      $dropdown .= "<option value='0'>Sin Categorias Disponibles</option>\n";
  }

  $dropdown .= "</select>\n";

  $html .= $dropdown;
	$html .= "<div id='league-data'></div>";
  $html .= "</div>";

  echo $html;
}

function cuicpro_teams_by_coach() {
	$html = "<div class='teams-wrapper'>";
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

	echo $html;
}

// function to register the dashboard widget
function teams_dashboard_widgets() {
	// Register your custom WordPress admin dashboard widget
	wp_add_dashboard_widget('cuicpro_teams_widget', 'CUICPRO Equipos por Categoria', 'cuicpro_teams_by_league');
	wp_add_dashboard_widget('cuicpro_teams_by_coach_widget', 'CUICPRO Equipos por Entrenador', 'cuicpro_teams_by_coach');
}

// hooks up your code to dashboard setup
add_action('wp_dashboard_setup', 'teams_dashboard_widgets');

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