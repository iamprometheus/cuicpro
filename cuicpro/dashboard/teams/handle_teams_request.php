<?php

function create_coaches_dropdown() {
  $html = "";
  $coaches = CoachesDatabase::get_coaches();
  foreach ($coaches as $coach) {
    $html .= "<option value='" . esc_attr($coach->coach_id) . "'>" . esc_html($coach->coach_name) . "</option>";
  }
  return $html;
}

function create_dynamic_input_team() {
  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='team-wrapper' id='dynamic-input-team'>";
  $html .= "<div class='team-input-cell'>
              <input type='text' id='team-name-ta' placeholder='Nombre'>
            </div>";
  $html .= "<div class='team-input-cell'>
              <input type='text' id='team-city-ta' placeholder='Ciudad'>
            </div>";
  $html .= "<div class='team-input-cell'>
              <input type='text' id='team-state-ta' placeholder='Estado'>
            </div>";
  $html .= "<div class='team-input-cell'>
              <input type='text' id='team-country-ta' placeholder='Pais'>
            </div>";
  $html .= "<div class='team-input-cell'>
              <select id='team-coach-ta'>
                <option value=''>Seleccionar Entrenador</option>
                " . create_coaches_dropdown() . "
              </select>
            </div>";
  $html .= "<div class='team-input-cell'>
              <input type='text' id='team-logo-ta' placeholder='Logo'>
            </div>";
  $html .= "<div class='team-cell'>
              <button id='add-team-button-tv'>Agregar</button>
            </div>";
  $html .= "</div>";

  return $html;
}

function fetch_league_data() {
  if (!isset($_POST['league_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener los equipos']);
  }
  $league_id = intval($_POST['league_id']);
  $teams = TeamsDatabase::get_teams_by_league($league_id);

  // create table header
  $html = "<div class='teams-wrapper'>
            <div class='teams-header'>
              <span class='team-cell'>Equipo: </span>
              <span class='team-cell'>Ciudad: </span>
              <span class='team-cell'>Estado: </span>
              <span class='team-cell'>Pais: </span>
              <span class='team-cell'>Entrenador: </span>
              <span class='team-cell'>Logo: </span>
              <span class='team-cell'>Acciones: </span>
            </div>
            ";

  // add team data to table
  foreach ($teams as $team) {
    $coach_name = CoachesDatabase::get_coach_by_id($team->coach_id)->coach_name;
    $html .= "<div class='team-wrapper' id='team-$team->team_id'>";
    $html .= "<span class='team-cell'>" . esc_html($team->team_name) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($team->city) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($team->state) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($team->country) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($coach_name) . "</span>";
    $html .= "<div class='team-cell'>
                <img src='http://test.local/$team->logo/'>
              </div>";
    $html .= "<div class='team-cell'>
                <button id='delete-team-button-tv' data-team-id=$team->team_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  $html .= create_dynamic_input_team();

  wp_send_json_success(['html' => $html]);
}

function fetch_coach_data() {
  if (!isset($_POST['coach_id'])) {
    wp_send_json_error(['message' => 'No se pudo obtener los equipos']);
  }
  $coach_id = intval($_POST['coach_id']);
  $teams = TeamsDatabase::get_teams_by_coach($coach_id);

  // create table header
  $html = "<div class='teams-wrapper'>
            <div class='teams-header'>
              <span class='team-cell'>Equipo: </span>
              <span class='team-cell'>Categoria: </span>
              <span class='team-cell'>Ciudad: </span>
              <span class='team-cell'>Estado: </span>
              <span class='team-cell'>Pais: </span>
              <span class='team-cell'>Logo: </span>
              <span class='team-cell'>Acciones: </span>
            </div>
            ";

  // add team data to table
  foreach ($teams as $team) {
    $league_name = LeaguesDatabase::get_league_by_id($team->league_id)->league_name;
    $html .= "<div class='team-wrapper' id='team-$team->team_id'>";
    $html .= "<span class='team-cell'>" . esc_html($team->team_name) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($league_name) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($team->city) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($team->state) . "</span>";
    $html .= "<span class='team-cell'>" . esc_html($team->country) . "</span>";
    $html .= "<div class='team-cell'>
                <img src='http://test.local/$team->logo/'>
              </div>";
    $html .= "<div class='team-cell'>
                <button id='delete-team-button-tv' data-team-id=$team->team_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  wp_send_json_success(['html' => $html]);
}

function delete_team() {
  if (!isset($_POST['team_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el equipo']);
  }
  $team_id = intval($_POST['team_id']);
  TeamsDatabase::delete_team($team_id);
  wp_send_json_success(['message' => 'Equipo eliminado correctamente']);
}

function add_team() {
  if (!isset($_POST['league_id']) || !isset($_POST['team_name']) || !isset($_POST['city']) || !isset($_POST['state']) || !isset($_POST['country']) || !isset($_POST['coach_id']) || !isset($_POST['logo'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $league_id = intval($_POST['league_id']);
  $team_name = sanitize_text_field($_POST['team_name']);
  $city = sanitize_text_field($_POST['city']);
  $state = sanitize_text_field($_POST['state']);
  $country = sanitize_text_field($_POST['country']);
  $coach_id = sanitize_text_field($_POST['coach_id']);
  $logo = sanitize_text_field($_POST['logo']);
  $result = TeamsDatabase::insert_team($team_name, $league_id, $city, $state, $country, $coach_id, $logo);
  if ($result) {
    $team = TeamsDatabase::get_team_by_name($team_name, $league_id);
    $coach_name = CoachesDatabase::get_coach_by_id($coach_id)->coach_name;
    $team->coach_name = $coach_name;
    wp_send_json_success(['message' => 'Equipo agregado correctamente', 'team' => $team]);
  }
  wp_send_json_error(['message' => 'Equipo no agregado, equipo ya existe']);
}

add_action('wp_ajax_fetch_coach_data', 'fetch_coach_data');
add_action('wp_ajax_nopriv_fetch_coach_data', 'fetch_coach_data');
add_action('wp_ajax_fetch_league_data', 'fetch_league_data');
add_action('wp_ajax_nopriv_fetch_league_data', 'fetch_league_data');
add_action('wp_ajax_delete_team', 'delete_team');
add_action('wp_ajax_nopriv_delete_team', 'delete_team');
add_action('wp_ajax_add_team', 'add_team');
add_action('wp_ajax_nopriv_add_team', 'add_team');
