<?php

function on_add_official($official) {
  $checked = $official->official_active ? 'checked' : '';
  return "<div class='officials-table-row' id='official-$official->official_id'>
            <span class='officials-table-cell'>$official->official_name</span>
            <span class='officials-table-cell'>$official->official_hours</span>
            <span class='officials-table-cell'>$official->official_schedule</span>
            <span class='officials-table-cell'>$official->official_mode</span>
            <span class='officials-table-cell'>$official->official_team_id</span>
            <span class='officials-table-cell'>$official->official_city</span>
            <span class='officials-table-cell'>$official->official_state</span>
            <span class='officials-table-cell'>$official->official_country</span>
            <div class='officials-table-cell'>	
              <input type='checkbox' id='active-official-button' $checked/>
            </div>
            <div class='officials-table-cell'>	
              <button id='edit-official-button' data-official-id='$official->official_id'>Editar</button>
              <button id='delete-official-button' data-official-id='$official->official_id'>Eliminar</button>
            </div>
          </div>";
}

function delete_official() {
  if (!isset($_POST['official_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el arbitro']);
  }
  $official_id = intval($_POST['official_id']);
  OfficialsDatabase::delete_official($official_id);
  wp_send_json_success(['message' => 'Arbitro eliminado correctamente']);
}

function add_official() {
  if (!isset($_POST['official_name']) || !isset($_POST['official_hours']) || !isset($_POST['official_schedule']) || !isset($_POST['official_mode']) || !isset($_POST['official_team_id']) || !isset($_POST['official_city']) || !isset($_POST['official_state']) || !isset($_POST['official_country'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $official_name = sanitize_text_field($_POST['official_name']);
  $official_hours = intval($_POST['official_hours']);
  $official_schedule = sanitize_text_field($_POST['official_schedule']);
  $official_mode = intval($_POST['official_mode']);
  $official_team_id = intval($_POST['official_team_id']);
  $official_team_id = $official_team_id === 0 ? null : $official_team_id;
  $official_city = sanitize_text_field($_POST['official_city']);
  $official_state = sanitize_text_field($_POST['official_state']);
  $official_country = sanitize_text_field($_POST['official_country']);

  $result = OfficialsDatabase::insert_official($official_name, $official_hours, $official_schedule, $official_mode, $official_team_id, $official_city, $official_state, $official_country);
  
  if ($result) {
    $active_tournament = TournamentsDatabase::get_active_tournament();
    if (!$active_tournament) {
      $tournament_days = "";
    } else {
      $tournament_days = $active_tournament->tournament_days;
    }

    $official = OfficialsDatabase::get_official_by_name($official_name);

    wp_send_json_success(['message' => 'Arbitro agregado correctamente', 'html' => on_add_official($official), 'tournament_days' => $tournament_days]);
  }
  wp_send_json_error(['message' => 'Arbitro no agregado, arbitro ya existe']);
}

add_action('wp_ajax_delete_official', 'delete_official');
add_action('wp_ajax_nopriv_delete_official', 'delete_official');
add_action('wp_ajax_add_official', 'add_official');
add_action('wp_ajax_nopriv_add_official', 'add_official');
