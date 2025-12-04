<?php

function render_modifications_handler()
{
  $html = "";
  $html .= "<div class='modifications-handler'>";
  $html .= "<span>Modificar partidos: </span>";
  $html .= "<button id='modify_matches_button'>Modificar</button>";
  $html .= "<button id='save_matches_button' disabled>Guardar cambios</button>";
  $html .= "</div>";
  return $html;
}

function generate_field_columns($tournament)
{
  $html = "";
  $fields5v5 = $tournament->tournament_fields_5v5;
  $fields7v7 = $tournament->tournament_fields_7v7;

  for ($i = 1; $i <= $fields5v5; $i++) {
    $html .= "<th data-field-type='1' data-field-number='" . esc_html($i) . "'>Campo 5v5 - " . esc_html($i) . "</th>";
  }
  for ($i = 1; $i <= $fields7v7; $i++) {
    $html .= "<th data-field-type='2' data-field-number='" . esc_html($i) . "'>Campo 7v7 - " . esc_html($i) . "</th>";
  }
  return $html;
}

function generate_cell_info($match)
{
  $html = "";
  $team1_name = "TBD";
  $team2_name = "TBD";

  if ($match->team_id_1) {
    $team1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  if ($team1_name === "TBD" && $match->match_link_1) {
    $team1_name = PendingMatchesDatabase::get_match_by_bracket_match_and_playoff($match->match_link_1, $match->bracket_id, $match->playoff_id)->bracket_match;
  }

  if ($team2_name === "TBD" && $match->match_link_2) {
    $team2_name = PendingMatchesDatabase::get_match_by_bracket_match_and_playoff($match->match_link_2, $match->bracket_id, $match->playoff_id)->bracket_match;
  }

  $division = DivisionsDatabase::get_division_by_id($match->division_id);

  $colors = [
    ['aqua', "black"],
    ['lightgreen', "black"],
    ['blue', "black"],
    ['pink', "black"],
    ['gray', "black"],
    ['green', "black"],
    ['lime', "black"],
    ['maroon', "black"],
    ['navy', "black"],
    ['olive', "black"],
    ['orange', "black"],
    ['purple', "white"],
    ['red', "black"],
    ['silver', "black"],
    ['teal', "black"],
    ['yellow', "black"],
    ['fuchsia', "white"]
  ];

  $color = $colors[$match->division_id % count($colors)];
  $playoff = "";
  $playoff_data = "";
  if ($match->match_type == 2) {
    $playoff = "<span style='font-weight: bold;'>ID: " . esc_html($match->bracket_match) . "</span>";
    $playoff .= "<span>Playoff - " . esc_html($match->playoff_id) . "</span>";
    $playoff_data .= "data-bracket-match='" . esc_html($match->bracket_match) . "'";
    $playoff_data .= "data-playoff-id='" . esc_html($match->playoff_id) . "'";
    $playoff_data .= "data-division-id='" . esc_html($match->division_id) . "'";
    $playoff_data .= "data-match-link-1='" . esc_html($match->match_link_1) . "'";
    $playoff_data .= "data-match-link-2='" . esc_html($match->match_link_2) . "'";
  }

  $html .= "<td ondrop='dropHandler(event)' dragover='dragoverHandler(event)'>
              <div movable_element='true' 
                class='match-cell-info' 
                style='background-color: $color[0]; color: $color[1]; padding: 10px;' 
                ondragstart='dragstartHandler(event)' 
                ondragover='dragoverHandler(event)' 
                id='cell_{$match->match_id}'
                data-team-1-id='{$match->team_id_1}'
                data-team-2-id='{$match->team_id_2}'
                data-match-type='{$match->match_type}' "
    . $playoff_data . "
                draggable='false'>"
    . $playoff .
    "<span>" . esc_html($division->division_name) . "</span>
                <span>" . esc_html($team1_name) . " vs " . esc_html($team2_name) . "</span>
              </div>
            </td>";

  return $html;
}

function generate_matches_rows($tournament)
{
  $html = "";
  $days = explode(",", $tournament->tournament_days);

  $matches = PendingMatchesDatabase::get_matches_by_tournament($tournament->tournament_id);
  $breaks = TournamentBreaksDatabase::get_tournament_breaks_by_tournament($tournament->tournament_id);

  $fields5v5 = $tournament->tournament_fields_5v5;
  $fields7v7 = $tournament->tournament_fields_7v7;

  foreach ($days as $index => $day) {
    $matches_this_day = array_filter($matches, function ($match) use ($day) {
      return $match->match_date === $day;
    });

    $breaks_this_day = array_filter($breaks, function ($break) use ($day) {
      return str_contains($break->tournament_days, $day);
    });

    $break_hours = [];
    foreach ($breaks_this_day as $break) {
      $break_hours[intval($break->tournament_break_hour)] = $break->tournament_break_reason;
    }

    // convert day to date
    $date = DateTime::createFromFormat('d/m/y', $day);
    // Set locale to Spanish
    $formatter = new IntlDateFormatter(
      'es_419',              // Spanish (Mexico)
      IntlDateFormatter::FULL,
      IntlDateFormatter::NONE,
      $date->getTimezone(),
      IntlDateFormatter::GREGORIAN,
      "EEEE d 'de' MMMM"                 // 'EEEE' gives full day name
    );

    $colspan = $fields5v5 + $fields7v7 + 1;
    $html .= "<tr id='day_{$index}'>";
    $html .= "<td colspan='$colspan' class='day-cell'>" . esc_html(strtoupper($formatter->format($date))) . "</td>";
    $html .= "</tr>";

    $header_counter = 0;

    for ($i = 7; $i <= 23; $i++) {
      if ($header_counter == 5) {
        $html .= "<tr id='day_{$index}'>";
        $html .= "<th class='headers-cell'>Campos / Horas</th>";
        $html .= generate_field_columns($tournament);
        $html .= "</tr>";
        $header_counter = 0;
      }
      $header_counter++;

      $html .= "<tr id='day_{$index}' data-hour='{$i}'>";
      $html .= "<td class='hour-cell'>" . esc_html($i) . ":00</td>";

      if (array_key_exists($i, $break_hours)) {
        $breakline = $colspan - 1;
        $html .= "<td colspan='$breakline' class='pause-cell'>" . esc_html($break_hours[$i]) . "</td>";
        $html .= "</tr>";
        continue;
      }

      $matches_this_hour = array_filter($matches_this_day, function ($match) use ($i) {
        return $match->match_time == $i;
      });

      for ($j = 1; $j <= $fields5v5; $j++) {
        $match = array_find($matches_this_hour, function ($match) use ($j) {
          return $match->field_number == $j && $match->field_type == 1;
        });
        if (!$match) {
          $html .= "<td ondrop='dropHandler(event)'><div movable_element='true' ondragstart='dragstartHandler(event)' ondragover='dragoverHandler(event)' draggable='false'></div></td>";
          continue;
        }
        $html .= generate_cell_info($match);
      }
      for ($j = 1; $j <= $fields7v7; $j++) {
        $match = array_find($matches_this_hour, function ($match) use ($j) {
          return $match->field_number == $j && $match->field_type == 2;
        });
        if (!$match) {
          $html .= "<td ondrop='dropHandler(event)'><div movable_element='true' ondragstart='dragstartHandler(event)' ondragover='dragoverHandler(event)' draggable='false'></div></td>";
          continue;
        }
        $html .= generate_cell_info($match);
      }
      $html .= "</tr>";
    }
  }
  return $html;
}

function create_table_for_matches_schedule($tournament)
{
  if (is_null($tournament)) {
    return "<h3>No hay torneos activos</h3>";
  }
  $html = "<table id='matches-schedule-table' border='1' align='center'>";
  $html .= "<thead id='matches-schedule-thead'>";
  $html .= "<th class='headers-cell'>Campos / Horas</th>";
  $html .= generate_field_columns($tournament);
  $html .= "</thead>";
  $html .= "<tbody>";
  $html .= generate_matches_rows($tournament);
  $html .= "</tbody>";
  $html .= "</table>";
  return $html;
}

function matches_schedule_viewer()
{
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }

  $html = "";
  $html .= "<div id='matches-schedule-container'>";
  $html .= create_table_for_matches_schedule($tournament);
  $html .= "</div>";
  echo $html;
}

// enqueue scripts related to this file
function enqueue_matches_schedule_scripts()
{
  wp_enqueue_style('matches-schedule-styles', plugins_url('/styles.css', __FILE__));
  wp_enqueue_script(
    'matches-schedule-script',
    plugins_url('/handle_matches_schedule_request.js', __FILE__),
    array('jquery'),
    null,
    true
  );

  // Pass the AJAX URL to JavaScript
  wp_localize_script('matches-schedule-script', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_matches_schedule_scripts');

require_once __DIR__ . '/handle_matches_schedule_request.php';
