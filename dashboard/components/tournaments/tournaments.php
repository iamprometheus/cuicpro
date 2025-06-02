<?php

// function to display the dropdown of leagues to show teams
function create_tournament_fields() {
  $html = "<div id='tournament-data'>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Nombre:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='tournament-name' placeholder='Nombre'>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Dias de juego:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='tournament-days' readonly placeholder='Dias de juego'>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Horarios:</span>
              <div id='hours-container' class='hours-container'>
                
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 5v5:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='fields-5v5-range' readonly style='border:0; color:#f6931f; font-weight:bold;'>
              </div>
              <div id='slider-fields-5v5' class='tournament-slider'></div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 7v7:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='fields-7v7-range' readonly style='border:0; color:#f6931f; font-weight:bold;'>
              </div>
              <div id='slider-fields-7v7' class='tournament-slider'></div>
            </div>";

  $html .= "<button id='add-tournament-button'>Crear</button>";
  $html .= "</div>"; 

  return $html;
}

function create_tournament_hours($tournament_id) {
  $tournament_hours = TournamentHoursDatabase::get_tournament_hours($tournament_id);
  $html = "";
  foreach ($tournament_hours as $tournament_hour) {
    $html .= "<span>" . esc_html($tournament_hour->tournament_day) . ": " . esc_html($tournament_hour->tournament_hours_start) . ":00 - " . esc_html($tournament_hour->tournament_hours_end) . ":00</span>";
  }

  return $html;
}

function cuicpro_tournament_viewer() {
  $tournament = TournamentsDatabase::get_active_tournament();

  if (!$tournament) {
    $html = create_tournament_fields();
    echo $html;
    return;
  }

  $tournament_days = str_replace(',', ', ', $tournament->tournament_days);

  // create table header
  $html = "<div class='tournament-data'>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Torneo:</span>
              <span class='tournament-table-cell'>" . esc_html($tournament->tournament_name) . "</span>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Calendario:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='tournament-selected-days' readonly value='$tournament_days'>
              </div>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Horarios:</span>
              <div id='tournament-hours' class='tournament-table-cell-column'>
              " . create_tournament_hours($tournament->tournament_id) . "
              </div>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 5v5:</span>
              <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_5v5_start) . " - " . esc_html($tournament->tournament_fields_5v5_end) . "</span>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 7v7:</span>
              <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_7v7_start) . " - " . esc_html($tournament->tournament_fields_7v7_end) . "</span>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Acciones:</span>
              <div class='tournament-table-cell-column'>
                <button id='start-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Iniciar Torneo</button>
                <button id='delete-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Eliminar</button>
              </div>
            </div>
            <div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Resultado:</span>
              <span class='tournament-table-cell' id='tournament-result-table'>Resultado de la accion.</span>
            </div>
          </div>
            ";

  echo $html;
}


// enqueue scripts related to this file
function enqueue_tournaments_scripts() {
	wp_enqueue_style( 'tournaments-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'tournaments-script',
			plugins_url('/handle_tournaments_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('tournaments-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_tournaments_scripts');

require_once __DIR__ . '/handle_tournaments_request.php';

?>
