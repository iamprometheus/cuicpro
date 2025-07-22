<?php

function on_add_division($division) {

  $division_mode = $division->division_mode == 1 ? "5v5" :  "7v7" ;
  $division_category = $division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto");
  $is_active = $division->division_is_active ? 'checked' : '';
  $division_preferred_days = str_replace(',', ', ', $division->division_preferred_days);
  $html = "";
  
  $html .= "<div class='table-row' id='division-$division->division_id'>
              <span class='table-cell'>" . esc_html($division->division_name) . "</span>
              <span class='table-cell'>" . esc_html($division_category) . "</span>
              <span class='table-cell'>" . esc_html($division_mode) . "</span>
              <span class='table-cell'>" . esc_html($division->division_min_teams) . "</span>
              <span class='table-cell'>" . esc_html($division->division_max_teams) . "</span>
              <span class='table-cell'>" . esc_html($division_preferred_days) . "</span>
              <div class='table-cell'>
                  <input type='checkbox' id='active-division-button' data-division-id=$division->division_id $is_active></input>
              </div>
              <div class='table-cell'>
                <button id='edit-division-button' data-division-id=$division->division_id>Editar</button>
                <button id='delete-division-button' data-division-id=$division->division_id>Eliminar</button>
              </div>
            </div>";

  return $html;
}

function delete_division() {
  if (!isset($_POST['division_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar la division']);
  }
  if (TeamsDatabase::get_teams_by_division(intval($_POST['division_id']))) {
    wp_send_json_error(['message' => 'No se pudo eliminar la division, hay equipos asociados']);
  }
  $division_id = intval($_POST['division_id']);
  $tournament_id = DivisionsDatabase::get_division_by_id($division_id)->tournament_id;
  $tournament_started = TournamentsDatabase::is_tournament_started($tournament_id);

  if ($tournament_started) {
    wp_send_json_error(['message' => 'No se pudo eliminar la division, el torneo ha comenzado']);
  }

  DivisionsDatabase::delete_division($division_id);
  wp_send_json_success(['message' => 'Division eliminada correctamente']);
}

function add_division() {
  if (!isset($_POST['division_name']) || 
      !isset($_POST['tournament_id']) || 
      !isset($_POST['division_mode']) || 
      !isset($_POST['division_category']) || 
      !isset($_POST['division_min_teams']) || 
      !isset($_POST['division_max_teams']) || 
      !isset($_POST['division_preferred_days'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $division_name = sanitize_text_field($_POST['division_name']);
  $tournament_id = intval($_POST['tournament_id']);
  $division_mode = intval($_POST['division_mode']);
  $division_category = intval($_POST['division_category']);
  $division_min_teams = intval($_POST['division_min_teams']);
  $division_max_teams = intval($_POST['division_max_teams']);
  $division_preferred_days = sanitize_text_field($_POST['division_preferred_days']);

  if ($division_preferred_days === "") {
    $division_preferred_days = TournamentsDatabase::get_tournament_by_id($tournament_id)->tournament_days;
  }
  
  $result = DivisionsDatabase::insert_division($division_name, $tournament_id, $division_mode, $division_min_teams, $division_max_teams, $division_category, $division_preferred_days);
  
  if ($result[0]) {
    $division = DivisionsDatabase::get_division_by_id($result[1]);
    $dropdown = "<option value='" . $division->division_id . "'>" . $division_name . "</option>";
    wp_send_json_success(['message' => 'Division agregada correctamente', 'html' => on_add_division($division), 'dropdown' => $dropdown]);
  }
  wp_send_json_error(['message' => 'Division no agregada, division ya existe']);
}

function edit_division() {
  if (!isset($_POST['division_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener la division']);
  }

  $division_id = intval($_POST['division_id']);

  $division = DivisionsDatabase::get_division_by_id($division_id);
  wp_send_json_success(['message' => 'Editando division.', 'division' => $division]);
}

function update_division() {
  if (!isset($_POST['division_id']) || !isset($_POST['division_name']) || !isset($_POST['division_mode']) || !isset($_POST['division_category']) || !isset($_POST['division_min_teams']) || !isset($_POST['division_max_teams']) || !isset($_POST['division_preferred_days'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }

  $division_id = intval($_POST['division_id']);
  $division_name = sanitize_text_field($_POST['division_name']);
  $division_mode = intval($_POST['division_mode']);
  $division_category = intval($_POST['division_category']);
  $division_min_teams = intval($_POST['division_min_teams']);
  $division_max_teams = intval($_POST['division_max_teams']);
  $division_preferred_days = sanitize_text_field($_POST['division_preferred_days']);
  $visible = true;

  $division = DivisionsDatabase::get_division_by_id($division_id);

  if ($division_preferred_days === "") {
    $division_preferred_days = TournamentsDatabase::get_tournament_by_id($division->tournament_id)->tournament_days;
  }
  $result = DivisionsDatabase::update_division($division_id, $division_name, $division_mode, $division_min_teams, $division_max_teams, $division_category, $division_preferred_days, $division->tournament_id, $visible);
  
  if ($result) {
    $division = DivisionsDatabase::get_division_by_id($division_id);
    wp_send_json_success(['message' => 'Division actualizada correctamente', 'html' => on_add_division($division)]);
  }
  wp_send_json_error(['message' => 'Division no actualizada, division ya existe']);
}

function update_division_active() {
  if (!isset($_POST['division_id']) || !isset($_POST['division_is_active'])) {
    wp_send_json_error(['message' => 'No se pudo obtener la division']);
  }

  $division_id = intval($_POST['division_id']);
  $division_is_active = intval($_POST['division_is_active']);

  $result = DivisionsDatabase::update_division_active($division_id, $division_is_active);
  if ($result) {
    wp_send_json_success(['message' => 'Division actualizada correctamente']);
  }
  wp_send_json_error(['message' => 'Division no actualizada']);
}

add_action('wp_ajax_edit_division', 'edit_division');
add_action('wp_ajax_nopriv_edit_division', 'edit_division');
add_action('wp_ajax_update_division', 'update_division');
add_action('wp_ajax_nopriv_update_division', 'update_division');
add_action('wp_ajax_delete_division', 'delete_division');
add_action('wp_ajax_nopriv_delete_division', 'delete_division');
add_action('wp_ajax_add_division', 'add_division');
add_action('wp_ajax_nopriv_add_division', 'add_division');
add_action('wp_ajax_update_division_active', 'update_division_active');
add_action('wp_ajax_nopriv_update_division_active', 'update_division_active');
