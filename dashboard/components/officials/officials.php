<?php

function render_teams()
{
  $teams = TeamsDatabase::get_teams();
  $html = "";

  $html .= "<option value='0'>Ninguno</option>";
  foreach ($teams as $team) {
    $html .= "<option value='" . $team->team_id . "'>" . $team->team_name . "</option>";
  }
  return $html;
}

function create_hours_select_input($tournament_hours)
{
  $html = "";
  foreach ($tournament_hours as $index => $hour) {
    $hours_start = intval($hour->tournament_hours_start);
    $hours_end = intval($hour->tournament_hours_end);
    $day = str_replace("/", "-", $hour->tournament_day);
    $html .= "<div id='official-day-$day' class='hours-selector-container'>";
    $html .= "<span>$hour->tournament_day</span>";
    $html .= "</div>";
    $html .= "<div class='hours-selector hidden' id='hours-selector-$day'>";
    $html .= "<span>Horas:</span>";
    $html .= "<div class='hours-selector-item'>";
    $html .= "<input type='checkbox' value='all' id='hours-selector-all'>";
    $html .= "<label for='hours-selector-all'> Todo el dia </label>";
    $html .= "</div>";

    for ($i = $hours_start; $i <= $hours_end; $i++) {
      $html .= "<div class='hours-selector-item'>";
      $html .= "<input type='checkbox' value='$i' id='hour-checkbox'>";
      $html .= "<label> $i:00 </label>";
      $html .= "</div>";
    }
    $html .= "</div>";
  }
  return $html;
}

function create_hours_viewer($official_id)
{
  $html = "";
  $official_hours = OfficialsHoursDatabase::get_official_hours($official_id);
  foreach ($official_hours as $official_hour) {
    $day_html = str_replace("/", "-", $official_hour->official_day);
    $html .= "<div class='hours-viewer-container'>";
    $html .= "<span>$official_hour->official_day</span>";
    $html .= "</div>";
    $html .= "<div class='hours-viewer hidden' id='hours-viewer-$day_html'>";
    $html .= "<span>Horas:</span>";

    foreach (explode(",", $official_hour->official_hours) as $hour) {
      $html .= "<div class='hours-viewer-item'>";
      $html .= "<input type='checkbox' value='$hour' checked disabled>";
      $html .= "<label> $hour:00 </label>";
      $html .= "</div>";
    }
    $html .= "</div>";
  }
  return $html;
}

function create_input_official()
{
  $tournament = TournamentsDatabase::get_active_tournaments();

  $tournament_hours = [];
  if (empty($tournament)) {
    $tournament_hours = [];
  } else {
    $tournament_hours = TournamentHoursDatabase::get_tournament_hours_by_tournament($tournament[0]->tournament_id);
  }

  $tournament_days =  "";
  if (empty($tournament)) {
    $tournament_days = "";
  } else {
    $tournament_days = str_replace(',', ', ', $tournament[0]->tournament_days);
  }

  $html = "";
  // dynamic input fields for adding teams
  $html .= "<div class='tournament-inputs' id='dynamic-input-official'>";
  $html .= "<div id='tournament-input-container' style='text-align: center; margin-bottom: 15px; font-size: 20px;'>
              <span style='font-weight: bold; '>Registro de arbitros</span>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Nombre: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='official-name' placeholder='Nombre'>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Contacto: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='official-contact' placeholder='Contacto'>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Dias: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='official-schedule' readonly value='$tournament_days'>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Horas: </span>
              <div class='tournament-table-cell'>
                <div id='official-hours'>
                  " . create_hours_select_input($tournament_hours) . "
                </div>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Modo: </span>
              <div class='tournament-table-cell'>
                <select id='official-mode'>
                  <option value='1'>5v5</option>
                  <option value='2'>7v7</option>
                  <option value='3'>Ambos</option>
                </select>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Equipo: </span>
              <div class='tournament-table-cell'>
                <select id='official-team-id'>
                  " . render_teams() . "
                </select>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Ciudad: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='official-city' placeholder='Ciudad'>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Estado: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='official-state' placeholder='Estado'>
              </div>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Pais: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='official-country' placeholder='Pais'>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Acciones: </span>
              <div class='tournament-table-cell'>
                <button id='add-official-button'>Agregar</button>
                <button id='update-official-button' class='hidden'>Actualizar</button>
                <button id='cancel-official-button' class='hidden'>Cancelar</button>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Resultado: </span>				
              <span class='tournament-table-cell' id='official-result-table'>Resultado de la accion.</span>
            </div>";

  $html .= "</div>";

  return $html;
}

function render_officials($tournament)
{

  $html = "<div  style='margin-bottom: 15px; font-size: 20px;'>
            <span style='font-weight: bold; '>Oficiales registrados en torneo seleccionado:</span>
          </div>";
  $html .= "<div class='table-row'>
            <span class='table-cell'>Nombre: </span>
            <span class='table-cell'>Contacto: </span>
            <span class='table-cell'>Dias: </span>
            <span class='table-cell'>Horas: </span>
            <span class='table-cell'>Modalidad: </span>
            <span class='table-cell'>Equipo: </span>
            <span class='table-cell'>Ubicacion: </span>
            <span class='table-cell'>¿Esta certificado?</span>
            <span class='table-cell'>¿Esta activo?</span>
            <span class='table-cell'>Acciones: </span>
            </div>
            ";

  if (is_null($tournament)) {
    return $html;
  }

  $officials = OfficialsDatabase::get_officials_by_tournament($tournament->tournament_id);
  // add team data to table
  foreach ($officials as $official) {
    $checked_active = $official->official_is_active ? 'checked' : '';
    $checked_certified = $official->official_is_certified ? 'checked' : '';
    $team = "";
    if (!$official->official_team_id) {
      $team = "Ninguno";
    } else {
      $team = TeamsDatabase::get_team_by_id($official->official_team_id)->team_name;
    }
    $official_mode = $official->official_mode === "1" ? "5v5" : ($official->official_mode === "2" ? "7v7" : "Ambos");
    $official_schedule = str_replace(",", ", ", $official->official_schedule);

    $html .= "<div class='table-row' id='official-$official->official_id'>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_name) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_contact) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official_schedule) . "</span>";
    $html .= "<span class='table-cell'>" . create_hours_viewer($official->official_id) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official_mode) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($team) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($official->official_city) . ", " . esc_html($official->official_state) . ", " . esc_html($official->official_country) . "</span>";
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
  }

  return $html;
}

function cuicpro_official_viewer()
{

  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }

  $html = "<div class='tab-content'>";
  $html .= create_tournament_list();
  $html .= "<div class='table-view-container'> ";
  $html .= create_input_official();
  // create table header

  $html .= "<div id='officials-data'>";
  $html .= render_officials($tournament);
  $html .= "</div>";
  $html .= "</div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_official_scripts()
{
  wp_enqueue_style('officials-styles', plugins_url('/styles.css', __FILE__));
  wp_enqueue_script(
    'official-script',
    plugins_url('/handle_officials_request.js', __FILE__),
    array('jquery'),
    null,
    true
  );

  // Pass the AJAX URL to JavaScript
  wp_localize_script('official-script', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}

add_action('admin_enqueue_scripts', 'enqueue_official_scripts');

require_once __DIR__ . '/handle_officials_request.php';
