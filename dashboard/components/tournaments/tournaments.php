<?php

// function to display the dropdown of leagues to show teams
function create_tournament_fields() {
  $html = "<div class='tournament-inputs'>
            <div style='text-align: center; margin-bottom: 15px; font-size: 20px;'>
              <span style='font-weight: bold; '>Registro de torneos</span>
            </div>
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
              <span class='tournament-table-cell-header'># Campos 5v5:</span>
              <div class='tournament-table-cell'>
                <input type='number' id='fields-5v5' min='0' value='1' style='border:0; color:black; font-weight:bold; width: 60px;'>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'># Campos 7v7:</span>
              <div class='tournament-table-cell'>
                <input type='number' id='fields-7v7' min='0' value='0' style='border:0; color:black; font-weight:bold; width: 60px;'>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Acciones:</span>
              <div class='tournament-table-cell'>
                <button id='add-tournament-button'>Crear</button>
								<button id='update-tournament-button' data-tournament-id='0' class='hidden'>Actualizar</button>
								<button id='cancel-tournament-button' class='hidden'>Cancelar</button>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Resultado:</span>
              <span class='tournament-table-cell' id='tournament-result-table'>Resultado de la accion.</span>
            </div>";

            
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Ayuda</span>
              <div class='tournament-table-cell'>
                <div style='display: flex; flex-direction: column; gap: 5px; padding: 5px 0;'>
                  <button id='create-tournament-help-button'>Creacion de torneos</button>
                  <button id='update-tournament-help-button'>Actualizacion de torneos</button>
                  <button id='delete-tournament-help-button'>Eliminacion de torneos</button>
                  <button id='tournament-matches-help-button'>Partidos</button>
                  <button id='tournament-officials-help-button'>Arbitros</button>
                  <button id='finish-tournament-help-button'>Finalizacion de torneo</button>
                </div>
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

function create_tournament_table($tournament, $has_matches, $has_officials_assigned, $has_pending_matches, $tournament_days) {
  $assign_officials_disabled = '';
  $unassign_officials_disabled = '';
  $select_bracket_type_disabled = '';
  $delete_matches_disabled = '';
  $finish_tournament_disabled = '';

  if ($has_matches) {
    $select_bracket_type_disabled = 'disabled';
    if ($has_pending_matches) {
      $finish_tournament_disabled = 'disabled';
    }
    if (!$has_officials_assigned) {
      $unassign_officials_disabled = 'disabled';
    }
    if ($has_officials_assigned) {
      $assign_officials_disabled = 'disabled';
    }
  }

  if (!$has_matches) {
    $finish_tournament_disabled = 'disabled';
    $assign_officials_disabled = 'disabled';
    $unassign_officials_disabled = 'disabled';
    $delete_matches_disabled = 'disabled';
  }

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
    <span class='tournament-table-cell-header'># Campos 5v5:</span>
    <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_5v5) . "</span>
  </div>
  <div class='tournament-table-row'>
    <span class='tournament-table-cell-header'># Campos 7v7:</span>
    <span class='tournament-table-cell'>" . esc_html($tournament->tournament_fields_7v7) . "</span>
  </div>
  <div class='tournament-table-row'>
    <span class='tournament-table-cell-header'>Acciones:</span>
    <div class='tournament-table-cell-column'>
      <button class='base-button pending-button' id='edit-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $select_bracket_type_disabled>Editar torneo</button>
      <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
      <span style='text-align: center;'>Tipo de torneo:</span>
      <button class='base-button pending-button' id='create-general-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $select_bracket_type_disabled>Generar Partidos (Liguilla + Playoffs)</button>
      <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
      <button class='base-button pending-button' id='assign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $assign_officials_disabled>Asignar Arbitros</button>
      <button class='base-button danger-button' id='unassign-officials-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $unassign_officials_disabled>Desasignar Arbitros</button>
      <hr style='background-color: black; height: 1px; width: 100%; margin: 0;'/>
      <button class='base-button danger-button' id='delete-matches-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $delete_matches_disabled>Eliminar Partidos</button>
      <button class='base-button danger-button' id='finish-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' $finish_tournament_disabled>Finalizar Torneo</button>
      <button class='base-button danger-button' id='delete-tournament-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "' >Eliminar Torneo</button>
    </div>
  </div>
  <div class='tournament-table-row' id='tournament-result-table-container'>
    <span class='tournament-table-cell-header'>Resultado:</span>
    <span class='tournament-table-cell' id='tournament-result-table-" . esc_attr($tournament->tournament_id) . "'>Resultado de la accion.</span>
  </div>
  </div>";

  return $html;
}

function cuicpro_tournament_viewer() {

  $html = "<div class='tournaments-container'>";
  
  $html .= create_tournament_fields();

  $html .= "<div style='display: flex; flex-direction: column;'>"; 
  $html .= "<div id='tournament-input-container' style='margin-bottom: 15px; font-size: 20px;'>
              <span style='font-weight: bold; '>Torneos Activos:</span>
            </div>";

  $tournaments = TournamentsDatabase::get_active_tournaments();
  
  
  $html .= "<div class='tournaments-container' id='tournaments-container'>";
  foreach ($tournaments as $tournament) {
    
    $brackets = BracketsDatabase::get_brackets_by_tournament($tournament->tournament_id);
    $pending_matches = PendingMatchesDatabase::get_pending_matches_by_tournament($tournament->tournament_id);
    $has_matches = empty($brackets) ? false : true;
    $has_officials_assigned = $tournament->tournament_has_officials == 1 ? true : false;
    $has_pending_matches = empty($pending_matches) ? false : true;

    $tournament_days = str_replace(',', ', ', $tournament->tournament_days);
    $html .= create_tournament_table($tournament, $has_matches, $has_officials_assigned, $has_pending_matches, $tournament_days);
  }

  $html .= "</div>";

  $html .= "</div>";
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
