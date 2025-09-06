<?php

function handle_filter_by_coach() {
  if (!isset($_POST['filter']) ) {
    wp_send_json_error(['message' => 'No se pudo obtener los jugadores']);
  }

  $filter_by = $_POST['filter'];

  if ($filter_by === "all") {
    $players = PlayersDatabase::get_players();
    $html = "<option value='all'>Todos los equipos</option>";
    wp_send_json_success(['message' => 'Jugadores obtenidos correctamente', 'filters' => $html, 'players' => render_players($players)]);
  } 
  
  $coach_id = intval($filter_by);
  $teams = TeamsUserDatabase::get_teams_by_coach($coach_id);
  $players = PlayersDatabase::get_players_by_coach($coach_id);
  $html = "";
  $html .= "<option value='all'>Todos los equipos</option>";
  foreach ($teams as $team) {
    $html .= "<option value='$team->team_id'>" . esc_html($team->team_name) . "</option>";
  }
  wp_send_json_success(['message' => 'Jugadores obtenidos correctamente', 'filters' => $html, 'players' => render_players($players)]);
  
}

function handle_filter_by_team() {
  if (!isset($_POST['filter']) ) {
    wp_send_json_error(['message' => 'No se pudo obtener los jugadores']);
  }

  $filter_by = $_POST['filter'];
  $coach_id = $_POST['coach'];

  if ($filter_by === "all") {
    $players = PlayersDatabase::get_players_by_coach($coach_id);
    wp_send_json_success(['message' => 'Jugadores obtenidos correctamente', 'players' => render_players($players)]);
  } 
  
  $team_id = intval($filter_by);
  $players = PlayersDatabase::get_players_by_team($team_id);
  wp_send_json_success(['message' => 'Jugadores obtenidos correctamente', 'players' => render_players($players)]);
  
}

function on_add_player($player) {
  $team = "";
  if ( !$player->team_id ) {
    $team = "Ninguno";
  } else {
    $team = TeamsUserDatabase::get_team_by_id($player->team_id)->team_name;
  }

  $html = "<div class='table-row' id='player-$player->player_id'>";
  $html .= "<span class='table-cell'>" . esc_html($player->player_name) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($team) . "</span>";
  // $html .= "<span class='table-cell'>" . wp_get_attachment_image($player->player_photo, 'thumbnail') . "</span>";
  $html .= "<div class='table-cell'>
              <button id='edit-player-button' data-player-id=$player->player_id>Editar</button>
              <button id='delete-player-button' data-player-id=$player->player_id>Eliminar</button>
            </div>";
  $html .= "</div>";
  return $html;
}

function delete_player_admin() {
  if (!isset($_POST['player_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el jugador']);
  }
  $player_id = intval($_POST['player_id']);

  PlayersDatabase::delete_player($player_id);
  wp_send_json_success(['message' => 'Jugador eliminado correctamente']);
}

// function add_official() {
//   if (!isset($_POST['official_name']) || !isset($_POST['official_hours']) || !isset($_POST['official_schedule']) || !isset($_POST['official_mode']) || !isset($_POST['official_team_id']) || !isset($_POST['official_city']) || !isset($_POST['official_state']) || !isset($_POST['official_country']) || !isset($_POST['tournament_id'])) {
//     wp_send_json_error(['message' => 'Faltan datos']);
//   }
//   $official_name = sanitize_text_field($_POST['official_name']);
//   $official_hours = $_POST['official_hours'];
//   $official_schedule = sanitize_text_field($_POST['official_schedule']);
//   $official_mode = intval($_POST['official_mode']);
//   $official_team_id = intval($_POST['official_team_id']);
//   $official_team_id = $official_team_id === 0 ? null : $official_team_id;
//   $official_city = sanitize_text_field($_POST['official_city']);
//   $official_state = sanitize_text_field($_POST['official_state']);
//   $official_country = sanitize_text_field($_POST['official_country']);
//   $tournament_id = intval($_POST['tournament_id']);

//   $result = OfficialsDatabase::insert_official($tournament_id, null, $official_name, $official_schedule, $official_mode, $official_team_id, $official_city, $official_state, $official_country);
  
//   if ($result[0]) {
//     $official = OfficialsDatabase::get_official_by_id($result[1]);

//     foreach ($official_hours as $day => $hours) {
//       $hours_str = implode(",", $hours);
//       OfficialsHoursDatabase::insert_official_hours($official->official_id, $day, $hours_str);
//     }
//     $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
//     wp_send_json_success(['message' => 'Arbitro agregado correctamente', 'html' => on_add_official($official), 'tournament_days' => $tournament->tournament_days]);
//   }
//   wp_send_json_error(['message' => 'Arbitro no agregado, arbitro ya existe']);
// }

function edit_player_admin() {
  if (!isset($_POST['player_id'])) {
    wp_send_json_error(['message' => 'No se pudo editar el jugador']);
  }
  $player_id = intval($_POST['player_id']);
  $player = PlayersDatabase::get_player_by_id($player_id);
  wp_send_json_success(['message' => 'Jugador editado correctamente', 'player' => $player]);
}

function update_player_admin() {
  if (!isset($_POST['player_id']) || !isset($_POST['player_name'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $player_id = intval($_POST['player_id']);
  $player_name = sanitize_text_field($_POST['player_name']);
  //$player_photo = $_FILES['player_photo'];

  // $attachment_id = null;
  // if ($player_photo['name'] == "") {
  //   $attachment_id = PlayersDatabase::get_player_by_id($player_id)->player_photo;
  // } else {
  //   addImageToWordPressMediaLibrary($player_photo['tmp_name'], $player_photo['name'], $player_photo['name'], $attachment_id);
  // }

  $result = PlayersDatabase::update_player(
    $player_id, 
    $player_name, 
    "");
  
  if ($result) {
    $player = PlayersDatabase::get_player_by_id($player_id);

    wp_send_json_success(['message' => 'Jugador actualizado correctamente', 'html' => on_add_player($player), 'player_id' => $player->player_id]);
  }
  wp_send_json_error(['message' => 'Jugador no actualizado, jugador ya existe']);
}

add_action('wp_ajax_filter_by_team', 'handle_filter_by_team');
add_action('wp_ajax_nopriv_filter_by_team', 'handle_filter_by_team');
add_action('wp_ajax_filter_by_coach', 'handle_filter_by_coach');
add_action('wp_ajax_nopriv_filter_by_coach', 'handle_filter_by_coach');
add_action('wp_ajax_edit_player_admin', 'edit_player_admin');
add_action('wp_ajax_nopriv_edit_player_admin', 'edit_player_admin');
add_action('wp_ajax_update_player_admin', 'update_player_admin');
add_action('wp_ajax_nopriv_update_player_admin', 'update_player_admin');
add_action('wp_ajax_delete_player_admin', 'delete_player_admin');
add_action('wp_ajax_nopriv_delete_player_admin', 'delete_player_admin');
// add_action('wp_ajax_add_player', 'add_player');
// add_action('wp_ajax_nopriv_add_player', 'add_player');
