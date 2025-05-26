<?php
function delete_coach() {
  if (!isset($_POST['coach_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el entrenador']);
  }
  if (TeamsDatabase::get_teams_by_coach(intval($_POST['coach_id']))) {
    wp_send_json_error(['message' => 'No se pudo eliminar el entrenador, hay equipos asociados']);
  }
  $coach_id = intval($_POST['coach_id']);
  CoachesDatabase::delete_coach($coach_id);
  wp_send_json_success(['message' => 'Entrenador eliminado correctamente']);
}

function add_coach() {
  if (!isset($_POST['coach_name']) || !isset($_POST['coach_contact']) || !isset($_POST['coach_city']) || !isset($_POST['coach_state']) || !isset($_POST['coach_country'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $coach_name = sanitize_text_field($_POST['coach_name']);
  $coach_contact = sanitize_text_field($_POST['coach_contact']);
  $coach_city = sanitize_text_field($_POST['coach_city']);
  $coach_state = sanitize_text_field($_POST['coach_state']);
  $coach_country = sanitize_text_field($_POST['coach_country']);
  $result = CoachesDatabase::insert_coach($coach_name, $coach_contact, $coach_city, $coach_state, $coach_country);
  if ($result) {
    $coach = CoachesDatabase::get_coach_by_name($coach_name);
    wp_send_json_success(['message' => 'Entrenador agregado correctamente', 'coach' => $coach]);
  }
  wp_send_json_error(['message' => 'Entrenador no agregado, entrenador ya existe']);
}

add_action('wp_ajax_delete_coach', 'delete_coach');
add_action('wp_ajax_nopriv_delete_coach', 'delete_coach');
add_action('wp_ajax_add_coach', 'add_coach');
add_action('wp_ajax_nopriv_add_coach', 'add_coach');
