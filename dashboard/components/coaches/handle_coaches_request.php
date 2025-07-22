<?php

function on_add_coach($coach) {
	$html = "<div class='table-row' id='coach-$coach->coach_id'>
						<span class='table-cell'>$coach->coach_name</span>
						<span class='table-cell'>$coach->coach_contact</span>
						<span class='table-cell'>$coach->coach_city</span>
						<span class='table-cell'>$coach->coach_state</span>
						<span class='table-cell'>$coach->coach_country</span>
						<div class='table-cell'>
							<button id='edit-coach-button' data-coach-id='$coach->coach_id'>Editar</button>
							<button id='delete-coach-button' data-coach-id='$coach->coach_id'>Eliminar</button>
						</div>
					</div>";
	return $html;
}

function delete_coach() {
  if (!isset($_POST['coach_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el entrenador']);
  }

  $coach_id = intval($_POST['coach_id']);
  
  $tournament_id = CoachesDatabase::get_coach_by_id($coach_id)->tournament_id;
  $tournament_started = TournamentsDatabase::is_tournament_started($tournament_id);

  if ($tournament_started) {
    wp_send_json_error(['message' => 'No se pudo eliminar el entrenador, el torneo ha comenzado']);
  }

  if (TeamsDatabase::get_teams_by_coach($coach_id)) {
    wp_send_json_error(['message' => 'No se pudo eliminar el entrenador, hay equipos asociados']);
  }
  CoachesDatabase::delete_coach($coach_id);
  wp_send_json_success(['message' => 'Entrenador eliminado correctamente']);
}

function add_coach() {
  if (!isset($_POST['coach_name']) || !isset($_POST['tournament_id']) || !isset($_POST['coach_contact']) || !isset($_POST['coach_city']) || !isset($_POST['coach_state']) || !isset($_POST['coach_country'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $coach_name = sanitize_text_field($_POST['coach_name']);
  $tournament_id = intval($_POST['tournament_id']);
  $coach_contact = sanitize_text_field($_POST['coach_contact']);
  $coach_city = sanitize_text_field($_POST['coach_city']);
  $coach_state = sanitize_text_field($_POST['coach_state']);
  $coach_country = sanitize_text_field($_POST['coach_country']);

  $result = CoachesDatabase::insert_coach(null, $tournament_id, $coach_name, $coach_contact, $coach_city, $coach_state, $coach_country);
  if ($result[0]) {
    $coach = CoachesDatabase::get_coach_by_id($result[1]);
    $html = on_add_coach($coach);
    $data = [
      'coach_id' => $coach->coach_id,
      'coach_name' => $coach->coach_name
    ];
    wp_send_json_success(['message' => 'Entrenador agregado correctamente', 'html' => $html, 'coach' => $data]);
  }
  wp_send_json_error(['message' => 'Entrenador no agregado, entrenador ya existe']);
}

function update_coach() {
  if (!isset($_POST['coach_id']) || !isset($_POST['coach_name']) || !isset($_POST['coach_contact']) || !isset($_POST['coach_city']) || !isset($_POST['coach_state']) || !isset($_POST['coach_country'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $coach_id = intval($_POST['coach_id']);
  $coach_name = sanitize_text_field($_POST['coach_name']);
  $coach_contact = sanitize_text_field($_POST['coach_contact']);
  $coach_city = sanitize_text_field($_POST['coach_city']);
  $coach_state = sanitize_text_field($_POST['coach_state']);
  $coach_country = sanitize_text_field($_POST['coach_country']);
  $visible = true;

  $result = CoachesDatabase::update_coach($coach_id, $coach_name, $coach_contact, $coach_city, $coach_state, $coach_country, $visible);
  if ($result) {
    $coach = CoachesDatabase::get_coach_by_id($coach_id);
    wp_send_json_success(['message' => 'Entrenador actualizado correctamente', 'html' => on_add_coach($coach)]);
  }
  wp_send_json_error(['message' => 'Entrenador no actualizado, entrenador ya existe']);
}

function edit_coach() {
  if (!isset($_POST['coach_id'])) {
    wp_send_json_error(['message' => 'No se pudo editar el entrenador']);
  }
  $coach_id = intval($_POST['coach_id']);
  $coach = CoachesDatabase::get_coach_by_id($coach_id);
  wp_send_json_success(['message' => 'Entrenador editado correctamente', 'coach' => $coach]);
}

add_action('wp_ajax_delete_coach', 'delete_coach');
add_action('wp_ajax_nopriv_delete_coach', 'delete_coach');
add_action('wp_ajax_add_coach', 'add_coach');
add_action('wp_ajax_nopriv_add_coach', 'add_coach');
add_action('wp_ajax_update_coach', 'update_coach');
add_action('wp_ajax_nopriv_update_coach', 'update_coach');
add_action('wp_ajax_edit_coach', 'edit_coach');
add_action('wp_ajax_nopriv_edit_coach', 'edit_coach');
