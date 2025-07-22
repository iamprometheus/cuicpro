<?php

function on_add_official($official) {
  $html = "";
  $checked_active = $official->official_is_active ? 'checked' : '';
  $checked_certified = $official->official_is_certified ? 'checked' : '';
  $team = "";
  if ( !$official->official_team_id ) {
    $team = "Ninguno";
  } else {
    $team = TeamsDatabase::get_team_by_id($official->official_team_id)->team_name;
  }

  $official_mode = $official->official_mode === "1" ? "5v5" : ($official->official_mode === "2" ? "7v7" : "Ambos");
  $official_schedule = str_replace(",", ", ", $official->official_schedule);

  $location = $official->official_city . ", " . $official->official_state . ", " . $official->official_country;
  $html .= "<div class='table-row' id='official-$official->official_id'>";
  $html .= "<span class='table-cell'>" . esc_html($official->official_name) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($official_schedule) . "</span>";
  $html .= "<span class='table-cell'>" . create_hours_viewer($official->official_id) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($official_mode) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($team) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($location) . "</span>";
  $html .= "<div class='table-cell'>
              <input type='checkbox' id='certified-official-button' data-official-id=$official->official_id $checked_certified>
            </div>";
  $html .= "<div class='table-cell'>
              <input type='checkbox' id='active-official-button' data-official-id=$official->official_id $checked_active>
            </div>";
  $html .= "<div class='table-cell'>
              <button id='edit-official-button' data-official-id=$official->official_id>Editar</button>
              <button id='delete-official-button' data-official-id=$official->official_id>Eliminar</button>
            </div>";
  $html .= "</div>";
  return $html;
}

function delete_official() {
  if (!isset($_POST['official_id'])) {
    wp_send_json_error(['message' => 'No se pudo eliminar el arbitro']);
  }
  $official_id = intval($_POST['official_id']);
  $tournament_id = OfficialsDatabase::get_official_by_id($official_id)->tournament_id;
  $tournament_started = TournamentsDatabase::is_tournament_started($tournament_id);

  if ($tournament_started) {
    wp_send_json_error(['message' => 'No se pudo eliminar el arbitro, el torneo ha comenzado']);
  }

  OfficialsDatabase::delete_official($official_id);
  wp_send_json_success(['message' => 'Arbitro eliminado correctamente']);
}

function add_official() {
  if (!isset($_POST['official_name']) || !isset($_POST['official_hours']) || !isset($_POST['official_schedule']) || !isset($_POST['official_mode']) || !isset($_POST['official_team_id']) || !isset($_POST['official_city']) || !isset($_POST['official_state']) || !isset($_POST['official_country']) || !isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $official_name = sanitize_text_field($_POST['official_name']);
  $official_hours = $_POST['official_hours'];
  $official_schedule = sanitize_text_field($_POST['official_schedule']);
  $official_mode = intval($_POST['official_mode']);
  $official_team_id = intval($_POST['official_team_id']);
  $official_team_id = $official_team_id === 0 ? null : $official_team_id;
  $official_city = sanitize_text_field($_POST['official_city']);
  $official_state = sanitize_text_field($_POST['official_state']);
  $official_country = sanitize_text_field($_POST['official_country']);
  $tournament_id = intval($_POST['tournament_id']);

  $result = OfficialsDatabase::insert_official($tournament_id, null, $official_name, $official_schedule, $official_mode, $official_team_id, $official_city, $official_state, $official_country);
  
  if ($result[0]) {
    $official = OfficialsDatabase::get_official_by_id($result[1]);

    foreach ($official_hours as $day => $hours) {
      $hours_str = implode(",", $hours);
      OfficialsHoursDatabase::insert_official_hours($official->official_id, $day, $hours_str);
    }
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    wp_send_json_success(['message' => 'Arbitro agregado correctamente', 'html' => on_add_official($official), 'tournament_days' => $tournament->tournament_days]);
  }
  wp_send_json_error(['message' => 'Arbitro no agregado, arbitro ya existe']);
}

function edit_official() {
  if (!isset($_POST['official_id'])) {
    wp_send_json_error(['message' => 'No se pudo editar el arbitro']);
  }
  $official_id = intval($_POST['official_id']);
  $official = OfficialsDatabase::get_official_by_id($official_id);
  $official_hours = OfficialsHoursDatabase::get_official_hours($official_id);
  wp_send_json_success(['message' => 'Arbitro editado correctamente', 'official' => $official, 'hours_data' => $official_hours]);
}

function update_official() {
  if (!isset($_POST['official_id']) || !isset($_POST['official_name']) || !isset($_POST['official_hours']) || !isset($_POST['official_schedule']) || !isset($_POST['official_mode']) || !isset($_POST['official_team_id']) || !isset($_POST['official_city']) || !isset($_POST['official_state']) || !isset($_POST['official_country'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $official_id = intval($_POST['official_id']);
  $official_name = sanitize_text_field($_POST['official_name']);
  $official_hours = $_POST['official_hours'];
  $official_schedule = sanitize_text_field($_POST['official_schedule']);
  $official_mode = intval($_POST['official_mode']);
  $official_team_id = intval($_POST['official_team_id']);
  $official_team_id = $official_team_id === 0 ? null : $official_team_id;
  $official_city = sanitize_text_field($_POST['official_city']);
  $official_state = sanitize_text_field($_POST['official_state']);
  $official_country = sanitize_text_field($_POST['official_country']);

  $result = OfficialsDatabase::update_official(
    $official_id, 
    $official_name, 
    $official_schedule, 
    $official_mode, 
    $official_team_id, 
    $official_city, 
    $official_state, 
    $official_country, 
    true);
  
  if ($result) {
    $official = OfficialsDatabase::get_official_by_id($official_id);
    $official_registered_hours = OfficialsHoursDatabase::get_official_hours($official_id);

    // delete days that are not in the new schedule
    $official_eliminated_days = array_filter($official_registered_hours, function($hour) use ($official_schedule) {
      return !str_contains($official_schedule, $hour->official_day);
    });
    
    $official_old_days = array_filter($official_registered_hours, function($hour) use ($official_schedule) {
      return str_contains($official_schedule, $hour->official_day);
    });

    foreach ($official_eliminated_days as $hour) {
      OfficialsHoursDatabase::delete_official_hours_by_id($hour->official_hours_id);
    }

    foreach ($official_hours as $day => $new_hours) {
      $hours_this_day = array_find($official_old_days, function($hour) use ($day) {
        return $hour->official_day === $day;
      });

      // check if the day is in the old schedule
      if (!$hours_this_day) {
        OfficialsHoursDatabase::insert_official_hours($official_id, $day, implode(",", $new_hours));
        continue;
      }
      
      // get the difference between the old hours and the new hours
      $hours_difference = array_diff(explode(",", $hours_this_day->official_hours), $new_hours);
      
      // merge old available hours with the new hours difference
      $new_available_hours = array_merge(explode(",", $hours_this_day->official_available_hours), $hours_difference);
      
      // convert arrays to strings
      $new_hours_str = implode(",", $new_hours);
      $new_available_hours_str = implode(",", $new_available_hours);
      
      OfficialsHoursDatabase::update_officials_hours($hours_this_day->official_hours_id, $new_hours_str, $new_available_hours_str);
    }
    $tournament = TournamentsDatabase::get_tournament_by_id($official->tournament_id);
    wp_send_json_success(['message' => 'Arbitro actualizado correctamente', 'html' => on_add_official($official), 'tournament_days' => $tournament->tournament_days]);
  }
  wp_send_json_error(['message' => 'Arbitro no actualizado, arbitro ya existe']);
}

function update_official_active() {
  if (!isset($_POST['official_id']) || !isset($_POST['official_is_active'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $official_id = intval($_POST['official_id']);
  $official_is_active = intval($_POST['official_is_active']);

  $result = OfficialsDatabase::update_official_active($official_id, $official_is_active);
  
  if ($result) {
    wp_send_json_success(['message' => 'Arbitro actualizado correctamente']);
  }
  wp_send_json_error(['message' => 'Arbitro no actualizado']);
}

function update_official_certified() {
  if (!isset($_POST['official_id']) || !isset($_POST['official_is_certified'])) {
    wp_send_json_error(['message' => 'Faltan datos']);
  }
  $official_id = intval($_POST['official_id']);
  $official_is_certified = intval($_POST['official_is_certified']);

  $result = OfficialsDatabase::update_official_certified($official_id, $official_is_certified);
  
  if ($result) {
    wp_send_json_success(['message' => 'Arbitro actualizado correctamente']);
  }
  wp_send_json_error(['message' => 'Arbitro no actualizado']);
}

add_action('wp_ajax_update_official_certified', 'update_official_certified');
add_action('wp_ajax_nopriv_update_official_certified', 'update_official_certified');
add_action('wp_ajax_edit_official', 'edit_official');
add_action('wp_ajax_nopriv_edit_official', 'edit_official');
add_action('wp_ajax_update_official', 'update_official');
add_action('wp_ajax_nopriv_update_official', 'update_official');
add_action('wp_ajax_update_official_active', 'update_official_active');
add_action('wp_ajax_nopriv_update_official_active', 'update_official_active');
add_action('wp_ajax_delete_official', 'delete_official');
add_action('wp_ajax_nopriv_delete_official', 'delete_official');
add_action('wp_ajax_add_official', 'add_official');
add_action('wp_ajax_nopriv_add_official', 'add_official');
