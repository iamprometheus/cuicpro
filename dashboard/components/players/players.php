<?php

function create_input_player()
{

  $html = "";
  // dynamic input fields for adding players
  $html .= "<div class='tournament-inputs' id='dynamic-input-player'>";
  $html .= "<div id='tournament-input-container' style='text-align: center; margin-bottom: 15px; font-size: 20px;'>
              <span style='font-weight: bold; '>Registro de jugadores</span>
            </div>";
  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Nombre: </span>
              <div class='tournament-table-cell'>
                <input type='text' id='player-name' placeholder='Nombre'>
              </div>
            </div>";


  // $html .= "<div class='tournament-table-row'>
  //             <span class='tournament-table-cell-header'>Foto: </span>
  //             <div class='tournament-table-cell'>	
  //               <input style='width: 80%;' type='file' id='player-photo-input' accept='.png, .jpg, .jpeg' placeholder='Seleccionar Foto'>
  //             </div>
  //           </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Acciones: </span>
              <div class='tournament-table-cell'>
                <button id='add-player-button' class='hidden'>Agregar</button>
                <button id='update-player-button' class='hidden'>Actualizar</button>
                <button id='cancel-player-button' class='hidden'>Cancelar</button>
              </div>
            </div>";

  $html .= "<div class='tournament-table-row'>
              <span class='tournament-table-cell-header'>Resultado: </span>				
              <span class='tournament-table-cell' id='player-result-table'>Resultado de la accion.</span>
            </div>";

  $html .= "</div>";

  return $html;
}

function render_player_filters($tournament)
{
  $players = [];
  $coaches = CoachesDatabase::get_coaches_by_tournament($tournament->tournament_id);

  foreach ($coaches as $coach) {
    // do something with each coach
    $add_players = PlayersDatabase::get_players_by_coach($coach->coach_id);
    $players = array_merge($players, $add_players);
  }

  $html = "<div class='filter-container' id='filter-container'>";
  $html .= "<span class='filter-container-header'>Filtros por coach:</span>";
  $html .= "<select id='filter-by-coach'>";
  $html .= "<option value='all'>Todos los coaches</option>";

  foreach ($coaches as $coach) {
    $html .= "<option value='$coach->coach_id'>" . esc_html($coach->coach_name) . "</option>";
  }

  $html .= "</select>";

  $html .= "<span class='filter-container-header'>Equipo:</span>";
  $html .= "<select id='filter-by-team'>";
  $html .= "<option value='all'>Todos los equipos</option>";
  $html .= "</select>";

  $html .= "</div>";

  $html .= "<div id='players-data-2'>";
  $html .= render_players($players);
  $html .= "</div>";

  return $html;
}

function render_players($players)
{

  $html = "<div  style='margin-bottom: 15px; font-size: 20px;'>
            <span style='font-weight: bold; '>Jugadores registrados en filtros seleccionados:</span>
          </div>";
  $html .= "<div class='table-row'>
            <span class='table-cell'>Nombre: </span>
            <span class='table-cell'>Equipo: </span>
            
            <span class='table-cell'>Acciones: </span>
            </div>
            ";

  // <span class='table-cell'>Foto: </span>

  // add team data to table
  foreach ($players as $player) {
    $team_name = "";
    if (!$player->team_id) {
      $team_name = "Ninguno";
    } else {
      $team_name = TeamsDatabase::get_team_by_id($player->team_id)->team_name;
    }

    $html .= "<div class='table-row' id='player-$player->player_id'>";
    $html .= "<span class='table-cell'>" . esc_html($player->player_name) . "</span>";
    $html .= "<span class='table-cell'>" . esc_html($team_name) . "</span>";
    // $html .= "<span class='table-cell'>" . wp_get_attachment_image($player->player_photo, 'thumbnail') . "</span>";
    $html .= "<div class='table-cell'>
                <button id='edit-player-button' data-player-id=$player->player_id>Editar</button>
                <button id='delete-player-button' data-player-id=$player->player_id>Eliminar</button>
              </div>";
    $html .= "</div>";
  }

  return $html;
}

function cuicpro_players_viewer()
{
  // get current first tournament 
  $tournaments = TournamentsDatabase::get_tournaments();
  $first_tournament = $tournaments ? $tournaments[0] : null;

  $html = "<div class='tab-content'>";

  $html .= create_tournament_list();
  $html .= "<div class='table-view-container'> ";
  $html .= create_input_player();
  // create table header

  $html .= "<div id='players-data-container'>";
  $html .= render_player_filters($first_tournament);
  $html .= "</div>";
  $html .= "</div>";
  $html .= "</div>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_players_scripts()
{
  wp_enqueue_script(
    'players-script',
    plugins_url('/handle_players_request.js', __FILE__),
    array('jquery'),
    null,
    true
  );

  // Pass the AJAX URL to JavaScript
  wp_localize_script('players-script', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_players_scripts');

require_once __DIR__ . '/handle_players_request.php';
