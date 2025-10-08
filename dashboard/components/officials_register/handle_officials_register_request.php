<?php

function switch_selected_tournament_register_officials()
{
  if (!isset($_POST['tournament_id'])) {
    wp_send_json_error(['message' => 'No se pudo iniciar el torneo']);
  }
  $tournament_id = intval($_POST['tournament_id']);
  $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
  if (!$tournament) {
    wp_send_json_error(['message' => 'No se pudo encontrar el torneo seleccionado.']);
  }

  wp_send_json_success([
    'message' => 'Torneo seleccionado correctamente',
    'pending_table' => render_pending_officials_from_register($tournament_id),
    'registered_table' => render_registered_officials_table($tournament_id)
  ]);
}

function reject_official_register()
{
  if (!isset($_POST['record_id'])) {
    wp_send_json_error(['message' => 'No se pudo rechazar el arbitro']);
  }
  $record_id = intval($_POST['record_id']);
  $pending_official = OfficialsRegisterQueueDatabase::get_officials_register_queue_by_id($record_id);
  if (!$pending_official) {
    wp_send_json_error(['message' => 'No se pudo encontrar el arbitro seleccionado.']);
  }

  $official_hours = OfficialsRegisterHoursDatabase::get_official_hours($record_id);
  foreach ($official_hours as $official_hour) {
    OfficialsRegisterHoursDatabase::delete_official_hours_by_id($official_hour->official_hours_id);
  }

  OfficialsRegisterQueueDatabase::delete_official($record_id);

  wp_send_json_success(['message' => 'Arbitro rechazado correctamente']);
}

function accept_official_register()
{
  if (!isset($_POST['record_id'])) {
    wp_send_json_error(['message' => 'No se pudo aceptar el arbitro']);
  }
  $record_id = intval($_POST['record_id']);
  $pending_official = OfficialsRegisterQueueDatabase::get_officials_register_queue_by_id($record_id);
  if (!$pending_official) {
    wp_send_json_error(['message' => 'No se pudo encontrar el arbitro seleccionado.']);
  }

  $tournament_id = intval($pending_official->tournament_id);
  $official_user_id = intval($pending_official->official_user_id);
  $official_name = $pending_official->official_name;
  $official_contact = $pending_official->official_contact;
  $official_schedule = $pending_official->official_schedule;
  $official_mode = intval($pending_official->official_mode);
  $official_city = $pending_official->official_city;
  $official_state = $pending_official->official_state;
  $official_country = $pending_official->official_country;

  // Register team
  $official_result = OfficialsDatabase::insert_official(
    $tournament_id,
    $official_user_id,
    $official_name,
    $official_contact,
    $official_schedule,
    $official_mode,
    null,
    $official_city,
    $official_state,
    $official_country,
  );

  if ($official_result[0]) {

    // register official hours
    $official_hours = OfficialsRegisterHoursDatabase::get_official_hours($record_id);

    $official_id = $official_result[1];
    foreach ($official_hours as $official_hour) {
      OfficialsHoursDatabase::insert_official_hours(
        $official_id,
        $official_hour->official_day,
        $official_hour->official_hours,
      );
    }

    foreach ($official_hours as $official_hour) {
      OfficialsRegisterHoursDatabase::delete_official_hours_by_id($official_hour->official_hours_id);
    }

    OfficialsRegisterQueueDatabase::delete_official($record_id);

    wp_send_json_success(['message' => 'Arbitro aceptado correctamente']);
  }

  wp_send_json_error(['message' => 'No se pudo registrar el arbitro.']);
}


add_action('wp_ajax_reject_official_register', 'reject_official_register');
add_action('wp_ajax_nopriv_reject_official_register', 'reject_official_register');
add_action('wp_ajax_accept_official_register', 'accept_official_register');
add_action('wp_ajax_nopriv_accept_official_register', 'accept_official_register');
add_action('wp_ajax_switch_selected_tournament_register_officials', 'switch_selected_tournament_register_officials');
add_action('wp_ajax_nopriv_switch_selected_tournament_register_officials', 'switch_selected_tournament_register_officials');
