<?php
function on_add_break($break)
{
  $break_days = str_replace(',', ', ', $break->tournament_days);
  $html = "<div class='table-row' id='break-$break->tournament_break_id'>";
  $html .= "<span class='table-cell'>" . esc_html($break_days) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($break->tournament_break_hour) . "</span>";
  $html .= "<span class='table-cell'>" . esc_html($break->tournament_break_reason) . "</span>";
  $html .= "<div class='table-cell'>
              <button id='edit-break-button' data-break-id=$break->tournament_break_id>Editar</button>
              <button id='delete-break-button' data-break-id=$break->tournament_break_id>Eliminar</button>
            </div>";
  $html .= "</div>";

  return $html;
}

function add_break()
{
  if (!isset($_POST['tournament_id']) || !isset($_POST['break_hour']) || !isset($_POST['break_reason']) || !isset($_POST['break_days'])) {
    wp_send_json_error(['message' => 'Faltan Datos']);
  }

  $tournament_id = intval($_POST['tournament_id']);
  $break_hour = intval($_POST['break_hour']);
  $break_reason = sanitize_text_field($_POST['break_reason']);
  $raw_break_days = $_POST['break_days'];

  $break_days = str_replace(' ', '', $raw_break_days);

  $result = TournamentBreaksDatabase::insert_tournament_breaks($tournament_id, $break_days, $break_hour, $break_reason);

  if ($result[0]) {
    $break = TournamentBreaksDatabase::get_tournament_breaks_by_id($result[1]);
    wp_send_json_success(['message' => 'Pausa agregada correctamente', 'html' => on_add_break($break)]);
  }

  wp_send_json_error(['message' => 'Error al agregar la pausa']);
}

function edit_break()
{
  if (!isset($_POST['break_id'])) {
    wp_send_json_error(['message' => 'Faltan Datos']);
  }
  $break_id = intval($_POST['break_id']);

  $result = TournamentBreaksDatabase::get_tournament_breaks_by_id($break_id);

  if ($result) {
    wp_send_json_success(['message' => 'Datos de pausa obtenidos correctamente', 'break' => $result]);
  }

  wp_send_json_error(['message' => 'Error al obtener los datos de la pausa']);
}

function update_break()
{
  if (!isset($_POST['break_id']) || !isset($_POST['break_hour']) || !isset($_POST['break_reason']) || !isset($_POST['break_days'])) {
    wp_send_json_error(['message' => 'Faltan Datos']);
  }
  $break_id = intval($_POST['break_id']);
  $break_hour = intval($_POST['break_hour']);
  $break_reason = sanitize_text_field($_POST['break_reason']);
  $break_days = $_POST['break_days'];

  $break_days = str_replace(' ', '', $break_days);

  $result = TournamentBreaksDatabase::update_tournament_break($break_id, $break_days, $break_hour, $break_reason);

  if ($result[0]) {
    $break = TournamentBreaksDatabase::get_tournament_breaks_by_id($break_id);
    wp_send_json_success(['message' => 'Pausa editada correctamente', 'html' => on_add_break($break)]);
  }

  wp_send_json_error(['message' => 'Error al editar la pausa']);
}

function delete_break()
{
  if (!isset($_POST['break_id'])) {
    wp_send_json_error(['message' => 'Faltan Datos']);
  }
  $break_id = intval($_POST['break_id']);

  $result = TournamentBreaksDatabase::delete_tournament_breaks_by_id($break_id);

  if ($result) {
    wp_send_json_success(['message' => 'Pausa eliminada correctamente']);
  }

  wp_send_json_error(['message' => 'Error al eliminar la pausa']);
}

add_action('wp_ajax_add_break', 'add_break');
add_action('wp_nopriv_add_break', 'add_break');
add_action('wp_ajax_update_break', 'update_break');
add_action('wp_nopriv_update_break', 'update_break');
add_action('wp_ajax_edit_break', 'edit_break');
add_action('wp_nopriv_edit_break', 'edit_break');
add_action('wp_ajax_delete_break', 'delete_break');
add_action('wp_nopriv_delete_break', 'delete_break');
