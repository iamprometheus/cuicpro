<?php

function create_input_division() {
	$html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='table-input'>";
  $html .= "<div id='tournament-input-container' style='margin-bottom: 15px; font-size: 20px;'>
							<span style='font-weight: bold; '>Registro de divisiones</span>
						</div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Nombre: </span>
							<div class='table-input-cell'>
								<input type='text' id='division-name' placeholder='Nombre'>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Modalidad: </span>
							<div class='table-input-cell'>
								<select id='division-mode'>
                  <option value='1'>5v5</option>
                  <option value='2'>7v7</option>
                </select>
							</div>
						</div>";
  $html .= "<div class='table-input-row'>
							<span class='table-cell'>Categoria: </span>
							<div class='table-input-cell'>
								<select id='division-category'>
                  <option value='1'>Varonil</option>
                  <option value='2'>Femenil</option>
                  <option value='3'>Mixto</option>
                </select>
							</div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Equipos Minimo: </span>
              <div class='table-input-cell'>
                <input type='number' id='division-min-teams' placeholder='Equipos Minimo' value='4' min='4'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
              <span class='table-cell'>Equipos Maximo: </span>
              <div class='table-input-cell'>
                <input type='number' id='division-max-teams' placeholder='Equipos Maximo' value='30' min='4'>
              </div>
            </div>";
  $html .= "<div class='table-input-row'>
						<span class='table-cell'>Acciones: </span>
							<div class='table-input-cell'>
                <button id='add-division-button'>Agregar</button>
								<button id='update-division-button' data-division-id='0' class='hidden'>Actualizar</button>
								<button id='cancel-division-button' class='hidden'>Cancelar</button>
							</div>
						</div>";
						
	$html .= "<div class='table-input-row'>
							<span class='table-cell'>Resultado: </span>				
							<span class='table-input-cell' id='division-result-table'>Resultado de la accion.</span>
						</div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_divisions($tournament) {
	$html = "<div style='margin-bottom: 15px; font-size: 20px;'>
            <span style='font-weight: bold; '>Divisiones en torneo seleccionado</span>
          </div>";
  $html .= "<div class='table-row'>
              <span class='table-cell'>Nombre: </span>
              <span class='table-cell'>Categoria: </span>
              <span class='table-cell'>Modalidad: </span>
              <span class='table-cell'>Equipos Minimo: </span>
              <span class='table-cell'>Equipos Maximo: </span>
              <span class='table-cell'>Activo: </span>
              <span class='table-cell'>Acciones: </span>
            </div>
            ";

  if (is_null($tournament)) {
    $html .= "<div class='table-row cell-hidden' id='division-data'></div>";
    return $html;
  }

  $divisions = DivisionsDatabase::get_divisions_by_tournament($tournament->tournament_id);

  // add team data to table
  if (empty($divisions)) {
    $html .= "<div class='table-row cell-hidden' id='division-data'></div>";
    return $html;
  }

  foreach ($divisions as $division) {
    $division->division_category = ($division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto"));
    $division->division_mode = ($division->division_mode == 1 ? "5v5" : ($division->division_mode == 2 ? "7v7" : "Ambos"));
    $is_active = $division->division_is_active ? 'checked' : '';
    
    $html .= "<div class='table-row' id='division-$division->division_id'>";
    $html .= "<span class='table-cell'>" . esc_html($division->division_name) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($division->division_category) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($division->division_mode) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($division->division_min_teams) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($division->division_max_teams) . "</span>";
    $html .= "<div class='table-cell'>
                <input type='checkbox' id='active-division-button' data-division-id=$division->division_id $is_active></input>
              </div>";
    $html .= "<div class='table-cell'>
                <button id='edit-division-button' data-division-id=$division->division_id>Editar</button>
                <button id='delete-division-button' data-division-id=$division->division_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  return $html;
}

function cuicpro_division_viewer() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }

  // create table header
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
  $html .= "<div class='table-view-container'>";
  $html .= create_input_division();
  $html .= "<div id='divisions-data'>";
  $html .= cuicpro_divisions($tournament);
  $html .= "</div>";
  $html .= "</div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_division_scripts() {
	wp_enqueue_style( 'divisions-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'division-script',
			plugins_url('/handle_divisions_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('division-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_division_scripts');

require_once __DIR__ . '/handle_divisions_request.php';