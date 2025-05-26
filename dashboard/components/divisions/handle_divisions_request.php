<?php

function on_add_division($division) {

  $division_mode = $division->division_mode == 1 ? "5v5" :  "7v7" ;
  $division_category = $division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto");
  $html = "";
  
  $html .= "<div class='division-wrapper' id='division-$division->division_id'>
              <span class='division-cell'>" . esc_html($division->division_name) . "</span>
              <span class='division-cell'>" . esc_html($division_category) . "</span>
              <span class='division-cell'>" . esc_html($division_mode) . "</span>
              <span class='division-cell'>" . esc_html($division->division_min_teams) . "</span>
              <span class='division-cell'>" . esc_html($division->division_max_teams) . "</span>
              <div class='team-cell'>
                <button id='delete-division-button-lv' data-division-id=$division->division_id>Eliminar</button>
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
  DivisionsDatabase::delete_division($division_id);
  wp_send_json_success(['message' => 'Division eliminada correctamente']);
}

function add_division() {
  if (!isset($_POST['division_name']) || 
      !isset($_POST['division_mode']) || 
      !isset($_POST['division_category']) || 
      !isset($_POST['division_min_teams']) || 
      !isset($_POST['division_max_teams'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  
  $active_tournament = TournamentsDatabase::get_active_tournament();
  if (!$active_tournament) {
    wp_send_json_error(['message' => 'No se pudo agregar la division, crea un torneo para agregar divisiones']);
  }

  $division_name = sanitize_text_field($_POST['division_name']);
  $division_mode = intval($_POST['division_mode']);
  $division_category = intval($_POST['division_category']);
  $division_min_teams = intval($_POST['division_min_teams']);
  $division_max_teams = intval($_POST['division_max_teams']);
  $tournament_id = $active_tournament->tournament_id;
  
  $result = DivisionsDatabase::insert_division($division_name, $division_mode, $division_min_teams, $division_max_teams, $division_category, $tournament_id);
  
  if ($result) {
    $division = DivisionsDatabase::get_division_by_name($division_name, $division_mode, $division_category, $tournament_id);
    $dropdown = "<option value='" . $division->division_id . "'>" . $division_name . "</option>";
    wp_send_json_success(['message' => 'Division agregada correctamente', 'html' => on_add_division($division), 'dropdown' => $dropdown]);
  }
  wp_send_json_error(['message' => 'Division no agregada, division ya existe']);
}

add_action('wp_ajax_delete_division', 'delete_division');
add_action('wp_ajax_nopriv_delete_division', 'delete_division');
add_action('wp_ajax_add_division', 'add_division');
add_action('wp_ajax_nopriv_add_division', 'add_division');
