<?php

function create_dynamic_input_division() {
  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='division-wrapper' id='dynamic-input-division'>";
  $html .= "<div class='division-input-cell'>
              <input type='text' id='division-name-lv' placeholder='Nombre'>
            </div>";
  $html .= "<div class='division-input-cell'>
              <select id='division-category-lv'>
                <option value='1'>Varonil</option>
                <option value='2'>Femenil</option>
                <option value='3'>Mixto</option>
              </select>
            </div>";
  $html .= "<div class='division-input-cell'>
              <select id='division-mode-lv'>
                <option value='1'>5v5</option>
                <option value='2'>7v7</option>
              </select>
            </div>";
  $html .= "<div class='division-input-cell'>
              <input type='number' id='division-min-teams-lv' placeholder='Equipos Minimo' value='4' min='4'>
            </div>";
  $html .= "<div class='division-input-cell'>
              <input type='number' id='division-max-teams-lv' placeholder='Equipos Maximo' value='30' min='4'>
            </div>";
  $html .= "<div class='division-cell'>
              <button id='add-division-button-lv'>Agregar</button>
            </div>";
  $html .= "</div>";

  return $html;
}

function cuicpro_division_viewer() {
  // create table header
  $html = "<div class='divisions-wrapper'>
            <div class='divisions-header'>
              <span class='division-cell'>Nombre: </span>
              <span class='division-cell'>Categoria: </span>
              <span class='division-cell'>Modalidad: </span>
              <span class='division-cell'>Equipos Minimo: </span>
              <span class='division-cell'>Equipos Maximo: </span>
              <span class='division-cell'>Acciones: </span>
            </div>
            ";

  $active_tournament = TournamentsDatabase::get_active_tournament();
  $divisions = $active_tournament ? DivisionsDatabase::get_divisions($active_tournament->tournament_id) : null;

  $html .= "<div id='divisions-data'>";

  // add team data to table
  if ($divisions) {
    foreach ($divisions as $division) {
      $division->division_category = ($division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto"));
      $division->division_mode = ($division->division_mode == 1 ? "5v5" : ($division->division_mode == 2 ? "7v7" : "Ambos"));
      $html .= "<div class='division-wrapper' id='division-$division->division_id'>";
      $html .= "<span class='division-cell'>" . esc_html($division->division_name) . "</span>";
      $html .= "<span class='division-cell'>" . esc_html($division->division_category) . "</span>";
      $html .= "<span class='division-cell'>" . esc_html($division->division_mode) . "</span>";
      $html .= "<span class='division-cell'>" . esc_html($division->division_min_teams) . "</span>";
      $html .= "<span class='division-cell'>" . esc_html($division->division_max_teams) . "</span>";
      $html .= "<div class='division-cell'>
                  <button id='delete-division-button-lv' data-division-id=$division->division_id>Eliminar</button>
                </div>";
      $html .= "</div>";
    }
  } else {
    $html .= "<div class='division-wrapper cell-hidden' id='division-data'>";
    $html .= "</div>";
  }


  $html .= create_dynamic_input_division();
  $html .= "</div> </div>";

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