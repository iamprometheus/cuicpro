<?php

function create_input_break()
{
  $tournament = TournamentsDatabase::get_active_tournaments();

  $tournament_days =  "";
  if (empty($tournament)) {
    $tournament_days = "";
  } else {
    $tournament_days = str_replace(',', ', ', $tournament[0]->tournament_days);
  }

  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='table-input'>";
  $html .= "<div id='tournament-input-container' style='margin-bottom: 15px; font-size: 20px;'>
							<span style='font-weight: bold; '>Registro de pausas</span>
						</div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Fechas: </span>
              <div class='table-input-cell'> 
                <input type='text' id='break-preferred-days' readonly value='$tournament_days'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Hora: </span>
              <div class='table-input-cell'>
                <div class='hours-slider'>
                  <div class='hours-slider-header'>
                    <label>Hora</label>
                    <input type='text' id='break-hour' readonly style='border:0; color:black; font-weight:bold; width: 100%;' value='12:00'>
                  </div>
                  <div id='slider-break-time' class='tournament-slider'></div>
						    </div>
              </div>
            </div>";

  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Motivo de la pausa: </span>
              <div class='table-input-cell'>
                <input type='text' id='break-reason' placeholder='Motivo'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
						<span class='table-cell'>Acciones: </span>
							<div class='table-input-cell'>
                <button id='add-break-button'>Agregar</button>
								<button id='update-break-button' data-break-id='0' class='hidden'>Actualizar</button>
								<button id='cancel-break-button' class='hidden'>Cancelar</button>
							</div>
						</div>";

  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Resultado: </span>				
							<span class='table-input-cell' id='break-result-table'>Resultado de la accion.</span>
						</div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_breaks($tournament)
{
  $html = "<div style='margin-bottom: 15px; font-size: 20px;'>
            <span style='font-weight: bold; '>Pausas en torneo seleccionado</span>
          </div>";
  $html .= "<div class='table-row'>
              <span class='table-cell'>Fecha: </span>
              <span class='table-cell'>Hora: </span>
              <span class='table-cell'>Motivo: </span>
              <span class='table-cell'>Acciones: </span>
            </div>";

  if (is_null($tournament)) {
    $html .= "<div class='table-row cell-hidden' id='break-data'></div>";
    return $html;
  }

  $breaks = TournamentBreaksDatabase::get_tournament_breaks_by_tournament($tournament->tournament_id);

  // add team data to table
  if (empty($breaks)) {
    $html .= "<div class='table-row cell-hidden' id='break-data'></div>";
    return $html;
  }

  foreach ($breaks as $break) {
    $break_days = str_replace(',', ', ', $break->tournament_days);
    $html .= "<div class='table-row' id='break-$break->tournament_break_id'>";
    $html .= "<span class='table-cell'>" . esc_html($break_days) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($break->tournament_break_hour) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($break->tournament_break_reason) . "</span>";
    $html .= "<div class='table-cell'>
                <button id='edit-break-button' data-break-id=$break->tournament_break_id>Editar</button>
                <button id='delete-break-button' data-break-id=$break->tournament_break_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  return $html;
}

function cuicpro_breaks_viewer()
{
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }

  // create table header
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
  $html .= "<div class='table-view-container'>";
  $html .= create_input_break();
  $html .= "<div id='breaks-data'>";
  $html .= cuicpro_breaks($tournament);
  $html .= "</div>";
  $html .= "</div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_breaks_scripts()
{
  wp_enqueue_style('breaks-styles', plugins_url('/styles.css', __FILE__));
  wp_enqueue_script(
    'breaks-script',
    plugins_url('/handle_breaks_request.js', __FILE__),
    array('jquery'),
    null,
    true
  );

  // Pass the AJAX URL to JavaScript
  wp_localize_script('breaks-script', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_breaks_scripts');

require_once __DIR__ . '/handle_breaks_request.php';
