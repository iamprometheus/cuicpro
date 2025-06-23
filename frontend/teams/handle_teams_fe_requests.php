<?php

function cuicpro_teams_render_teams_response($active_tournament) {
  if ($active_tournament == null) {
    return;
  }

  $teams = TeamsDatabase::get_enrolled_teams_by_tournament($active_tournament->tournament_id);
  if (empty($teams)) {
    return "<span>No hay equipos inscritos</span>";
  }
  $tddata = "";

  foreach ($teams as $team) {
    // check if image exists in db
    $logo = $team->logo;
    $logo_url = wp_get_attachment_image_url($logo, 'full');
    if (!$logo_url) {
      $logo_url = base_url()."default_team_logo.png";
    }
    
    $team_name = $team->team_name;
    $tddata.="<div class='teams-grid-item'>
              <img src='".$logo_url."' width='25' height='25' />
              <p>"
                .$team_name
              ."</p>
            </div>";
  }
  return $tddata;
}

function fetch_tournament_teams_display() {
    if (!isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Tournament ID is required!'));
    }
    
    // Your PHP logic here
    $tournament_id = $_POST['tournament_id'];
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    $html = cuicpro_teams_render_teams_response($tournament);

    wp_send_json_success(array('message' => 'Teams fetched successfully!', 'html' => $html));
}

add_action('wp_ajax_fetch_tournament_teams_display', 'fetch_tournament_teams_display');
add_action('wp_ajax_nopriv_fetch_tournament_teams_display', 'fetch_tournament_teams_display');
