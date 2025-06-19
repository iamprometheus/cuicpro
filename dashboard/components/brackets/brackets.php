<?php

function generate_brackets_dropdown(int $tournament_id) {
  $html = "";
  $brackets = BracketsDatabase::get_brackets_by_tournament($tournament_id);
  if (empty($brackets)) {
    $html .= "<option value='0'>No hay brackets</option>";
    return $html;
  }

  $html .= "<option value='0'>Selecciona una division</option>";
  foreach ($brackets as $bracket) {
    $division = DivisionsDatabase::get_division_by_id(intval($bracket->division_id));
    $mode = ModesDatabase::get_mode_by_id($division->division_mode);
    $category = CategoriesDatabase::get_category_by_id($division->division_category);
    
    $html .= "<option value='" . $bracket->bracket_id . "'>" . $division->division_name . " " . $mode->mode_description. " " . $category->category_description . "</option>";
  }
  return $html;
}

function cuicpro_brackets_viewer() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }
  
  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
  $html .= "
  <div class='brackets-wrapper'>
    <div class='brackets-header'>
      <span class='brackets-title'>Brackets</span>

      <div class='brackets-dropdown-container'>
        <button id='brackets-reload-button'>Recargar Resultados</button>
        <select id='brackets-dropdown'>
          " . generate_brackets_dropdown($tournament->tournament_id) . "
        </select>
      </div>
    </div>
    <div id='brackets-data' class='brackets-data'>
    </div>
  </div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_brackets_scripts() {
	wp_enqueue_style( 'brackets-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'brackets-script',
			plugins_url('/handle_brackets_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('brackets-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_brackets_scripts');

require_once __DIR__ . '/handle_brackets_request.php';