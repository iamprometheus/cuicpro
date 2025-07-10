<?php
function render_match_response($match) {
  $team_1_name = "TBD";
  $team_2_name = "TBD";

  if ($match->team_id_1) {
    $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  $official = OfficialsDatabase::get_official_by_id($match->official_id);

  $official_name = "Asignacion Pendiente";
  if ($official) {
    $official_name = $official->official_name;
  }

  $match_time = $match->match_time . ":00";
  
  $html = "<div class='match-teams'>";
  $html .= "<span>" . $team_2_name . "</span>";
  $html .= "<span>VS</span>";
  $html .= "<span>" . $team_1_name . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-info'>";
  $html .= "<div class='match-date'>";
  $html .= "<span>Fecha: " . $match->match_date . "</span>";
  $html .= "<span>Hora: " . $match_time . "</span>";
  $html .= "</div>";
  $html .= "<div class='match-game'>";
  $html .= "<span>Arbitro: " . $official_name . "</span>";
  $html .= "<span>Campo: " . $match->field_number . "</span>";
  $html .= "</div>";
  $html .= "</div>";
  return $html;
}

function render_matches_response($division_id) {
  // check if bracket exists
  $bracket = BracketsDatabase::get_bracket_by_division_id($division_id); 
  if ($bracket == null) {
    return "";
  }
  // check if there's matches for this bracket
  $matches = PendingMatchesDatabase::get_matches_by_bracket($bracket->bracket_id);
  if ($matches == null) {
    return "";
  }

  $matchcontainer = "<div class='division-matches' id='division-matches'>
                  <div class='division-matches-title'>
                    <span>Proximos partidos</span>
                  </div>
                  <div class='matches-container' id='matches-container' hidden>";
                  
  foreach ($matches as $match) {
    $matchcontainer .= "<div class='match-container'>
                    ".render_match_response($match)."
                  </div>";
    }
                  
  $matchcontainer .= "</div></div>";
  return $matchcontainer;
}

function render_teams_for_division_response($division_id) {
  $teams = TeamsDatabase::get_enrolled_teams_by_division($division_id);
  if (empty($teams)) {
    return "<span>No hay equipos inscritos en esta division</span>";
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
    $tddata.="<div class='team-container'>
              <img src='$logo_url' width='50' height='50' />
              <p>"
                .$team_name
              ."</p>
            </div>";
  }
  return $tddata;
}

function render_divisions_response($active_tournament) {
  if ($active_tournament == null) {
    return;
  }
  $divisions = DivisionsDatabase::get_active_divisions_by_tournament($active_tournament->tournament_id);
  if (empty($divisions)) {
    return;
  }
  
  $html = "";
  foreach ($divisions as $division) {
      $division_name = $division->division_name;
      $division_id = $division->division_id;
      $division_category = $division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto");
      $division_mode = $division->division_mode == 1 ? "5v5" : "7v7";
      $html .= "<div class='division-container' >
              <div id='division-name' class='division-title'>
                <span>".$division_name." ".$division_category." ".$division_mode."</span>
              </div>
              <div hidden class='teams-container'>"
                  .render_teams_for_division_response($division_id)
              ."</div>"
              .render_matches_response($division_id) 
            ."</div>";
  }
  return $html;
}

function fetch_tournament_divisions_display() {
    if (!isset($_POST['tournament_id'])) {
        wp_send_json_error(array('message' => 'Tournament ID is required!'));
    }
    
    // Your PHP logic here
    $tournament_id = $_POST['tournament_id'];
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    $html = render_divisions_response($tournament);

    wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'html' => $html));
}

add_action('wp_ajax_fetch_tournament_divisions_display', 'fetch_tournament_divisions_display');
add_action('wp_ajax_nopriv_fetch_tournament_divisions_display', 'fetch_tournament_divisions_display');
