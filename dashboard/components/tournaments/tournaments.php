<?php

// function to display the dropdown of leagues to show teams
function create_tournament_fields() {
  $html = "<div class='tournament-inputs'>
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
              <div id='hours-container' class='tournament-table-cell'>
                
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 5v5:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='fields-5v5-range' readonly style='border:0; color:black; font-weight:bold;'>
                <div id='slider-fields-5v5' class='tournament-slider'></div>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Campos 7v7:</span>
              <div class='tournament-table-cell'>
                <input type='text' id='fields-7v7-range' readonly style='border:0; color:black; font-weight:bold;'>
                <div id='slider-fields-7v7' class='tournament-slider'></div>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Acciones:</span>
              <div class='tournament-table-cell'>
                <button id='add-tournament-button'>Crear</button>
              </div>
            </div>";
  $html .= "</div>"; 

  return $html;
}

function create_tournament_hours($tournament_id) {
  $tournament_hours = TournamentHoursDatabase::get_tournament_hours_by_tournament($tournament_id);
  $html = "";
  foreach ($tournament_hours as $tournament_hour) {
    $html .= "<span>" . esc_html($tournament_hour->tournament_day) . ": " . esc_html($tournament_hour->tournament_hours_start) . ":00 - " . esc_html($tournament_hour->tournament_hours_end) . ":00</span>";
  }

  return $html;
}

function create_tournament_table($tournament, $has_matches, $has_officials, $tournament_days) {
  $add_officials_disabled = $has_matches && $has_officials ? 'disabled' : '';
  $unassign_officials_disabled = $has_matches && !$has_officials ? 'disabled' : '';
  $select_bracket_type_disabled = $has_matches ? 'disabled' : '';
  $delete_matches_disabled = !$has_matches ? 'disabled' : '';

  $html = "<div class='tournament-data' id='tournament-" . esc_attr($tournament->tournament_id) . "'>
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
      <span style='text-align: center;'>Tipo de torneo:</span>
      <button class='base-button pending-button' id='create-brackets-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'$select_bracket_type_disabled>Eliminacion directa</button>
      <button class='base-button pending-button' id='create-round-robin-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'$select_bracket_type_disabled>Liguilla</button>
      <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
      <button class='base-button pending-button' id='assign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $add_officials_disabled>Asignar Arbitros</button>
      <button class='base-button danger-button' id='unassign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $unassign_officials_disabled>Desasignar Arbitros</button>
      <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
      <button class='base-button danger-button' id='delete-matches-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $delete_matches_disabled>Eliminar Partidos</button>
      <button class='base-button danger-button' id='finish-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' disabled>Finalizar Torneo</button>
      <button class='base-button danger-button' id='delete-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' >Eliminar Torneo</button>
    </div>
  </div>
  <div class='tournament-table-row'>
    <span class='tournament-table-cell-header'>Resultado:</span>
    <span class='tournament-table-cell' id='tournament-result-table-" . esc_attr($tournament->tournament_id) . "'>Resultado de la accion.</span>
  </div>
</div>";

return $html;
}

function cuicpro_tournament_viewer() {

  $html = "<div class='tournaments-container'>";
  
  $html .= create_tournament_fields();

  $tournaments = TournamentsDatabase::get_active_tournaments();
  
  foreach ($tournaments as $tournament) {
    
    $brackets = BracketsDatabase::get_brackets_by_tournament($tournament->tournament_id);
    $has_matches = $brackets ? true : false;
    $has_officials = $tournament->tournament_has_officials == 1 ? true : false;

    $tournament_days = str_replace(',', ', ', $tournament->tournament_days);
    $html .= create_tournament_table($tournament, $has_matches, $has_officials, $tournament_days);
  }

  

  $html .= "</div>";
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
