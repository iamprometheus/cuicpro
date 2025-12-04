<?php

function render_team_match($match, $team_id)
{
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  $official_name = "Por Asignar";
  if ($match->official_id) {
    $official_name = OfficialsDatabase::get_official_by_id($match->official_id)->official_name;
  }

  $match_time = $match->match_time . ":00";
  $match_type = "Partido para puntos";

  if ($match->match_type == 2) {
    match ($match->bracket_reverse_round) {
      '1' => $match_type = 'Final',
      '2' => $match_type = 'Semifinales',
      '3' => $match_type = 'Cuartos de final',
      '4' => $match_type = 'Octavos de final',
      default => $match_type = 'Playoffs: Primera ronda'
    };
  }

  $is_potential_match = ($team_1_name == "TBD" && $team_2_name == "TBD") ||
    ($team_1_name == "TBD" && $team_id != $match->team_id_2) ||
    ($team_2_name == "TBD" && $team_id != $match->team_id_1);

  if ($is_potential_match) {
    $potential_match_type = '';
    match ($match->bracket_reverse_round) {
      '1' => $potential_match_type = 'final',
      '2' => $potential_match_type = 'semifinales',
      '3' => $potential_match_type = 'cuartos de final',
      '4' => $potential_match_type = 'octavos de final',
      default => $potential_match_type = 'Playoffs: Primera ronda'
    };
    $match_type = "Partido potencial (" . $potential_match_type . "), gana tus partidos para seguir avanzando!";
  }

  $html = "<div style='text-align: center;'>";
  $html .= "<span style='font-weight: bold; font-size: 18px;'>" . $match_type . "</span>";
  $html .= "</div>";
  $html .= "<hr>";
  $html .= "<div class='bracket-match'>";
  $html .= "<span>" . $team_1_name . "</span>";
  $html .= "<span>VS</span>";
  $html .= "<span>" . $team_2_name . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data-container'>";
  $html .= "<div class='match-data'>";
  $html .= "<span>Fecha: " . $match->match_date . "</span>";
  $html .= "<span>Hora: " . $match_time . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-data text-right'>";
  $html .= "<span>Arbitro: " . $official_name . "</span>";
  $html .= "<span>Campo: " . $match->field_number . "</span>";
  $html .= "</div>";
  $html .= "</div>";
  return $html;
}

function render_potential_matches($playoff_match)
{
  $current_match = $playoff_match;
  $html = "";
  while (true) {
    $next_match = PendingMatchesDatabase::get_match_by_bracket_match_to_link($current_match->bracket_match, $current_match->bracket_id);
    if (!$next_match) {
      break;
    }
    $current_match = $next_match;

    $html .= "<div class='bracket-match-container'>";
    $html .= render_team_match($current_match, null);
    $html .= "</div>";
  }
  return $html;
}

function render_teams_for_matches()
{
  $user_id = get_current_user_id();
  $teams = TeamsUserDatabase::get_teams_by_coach($user_id);

  $html = "<h2 style='text-align: center;'>Mis Equipos</h2>";
  $html .= "<div class='user-teams'>";
  foreach ($teams as $team) {
    $html .= "<div data-team-id='" . esc_attr($team->team_id) . "' class='team-item' id='team-item-matches'>";
    $html .= "<img class='team-logo' src='" . wp_get_attachment_image_url($team->team_logo, 'full') . "' alt='" . $team->team_name . "' />";
    $html .= "<span id='team-" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</span>";
    $html .= "</div>";
  }
  $html .= "</div>";
  return $html;
}

function render_matches_by_team($team_id)
{
  $html = "<div class='info-header'>
						<span id='back-button' data-screen='user-teams-matches' data-team-id='" . esc_attr($team_id) . "'>Volver</span>
						<h2>Partidos</h2>
					</div>";

  $team = TeamsDatabase::get_team_by_teams_team_id($team_id);
  if (!$team) {
    $html .= "<div>";
    $html .= "<span>Este equipo no se encuentra registrado en ningun torneo.</span>";
    $html .= "</div>";
    return $html;
  }

  $matches = PendingMatchesDatabase::get_matches_by_team($team->team_id, $team->tournament_id);

  $html .= "<div class='user-matches'>";
  foreach ($matches as $match) {
    $html .= "<div class='bracket-match-container'>";
    $html .= render_team_match($match, null);
    $html .= "</div>";
  }

  $playoff_matches = array_filter($matches, function ($match) {
    return $match->match_type == 2;
  });

  $playoff_matches = array_values($playoff_matches);

  if (count($playoff_matches) > 0) {
    $playoff_match = $playoff_matches[0];
    $html .= render_potential_matches($playoff_match);
  }

  if (empty($matches)) {
    $html .= "<div>";
    $html .= "<h3>No hay partidos pendientes para este equipo</h3>";
    $html .= "</div>";
  }

  $html .= "</div>";
  return $html;
}

function render_matches_by_team_player()
{
  $user = wp_get_current_user();
  $player_instances = PlayersDatabase::get_player_by_user_id($user->ID);
  $player = $player_instances[count($player_instances) - 1];

  if (!$player->team_id) {
    $html = "<div class='info-header'>
						<h2>Mis Partidos</h2>
					</div>";
    $html .= "<div>";
    $html .= "<h3>No tienes equipo asignado</h3>";
    $html .= "</div>";
    return $html;
  }

  $team = TeamsDatabase::get_team_by_id($player->team_id);
  $matches = PendingMatchesDatabase::get_matches_by_team($team->team_id, $team->tournament_id);

  $html = "<div class='info-header'>
						<h2>Mis Partidos</h2>
					</div>";
  $html .= "<div>";
  $html .= "<div class='user-matches'>";
  foreach ($matches as $match) {
    $html .= "<div class='bracket-match-container'>";
    $html .= render_team_match($match, $player->team_id);
    $html .= "</div>";
  }

  $playoff_matches = array_filter($matches, function ($match) {
    return $match->match_type == 2;
  });

  $playoff_matches = array_values($playoff_matches);

  if (count($playoff_matches) > 0) {
    $playoff_match = $playoff_matches[0];
    $html .= render_potential_matches($playoff_match);
  }

  if (empty($matches)) {
    $html .= "<div>";
    $html .= "<h3>No hay partidos pendientes para tu equipo</h3>";
    $html .= "</div>";
  }

  $html .= "</div>";
  $html .= "</div>";
  return $html;
}

function handle_fetch_matches_by_team()
{
  if (!isset($_POST['team_id'])) {
    wp_send_json_error(array('message' => 'Faltan datos'));
    return;
  }

  $team_id = $_POST['team_id'];
  wp_send_json_success(array('html' => render_matches_by_team($team_id)));
}

add_action('wp_ajax_handle_fetch_matches_by_team', 'handle_fetch_matches_by_team');
add_action('wp_ajax_nopriv_handle_fetch_matches_by_team', 'handle_fetch_matches_by_team');
