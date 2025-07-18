<?php

function generate_field_columns($tournament) {
  $html = "";
  $fields5v5 = $tournament->tournament_fields_5v5;
  $fields7v7 = $tournament->tournament_fields_7v7;

  for ($i = 1; $i <= $fields5v5; $i++) {
    $html .= "<th>Campo 5v5 - " . esc_html($i) . "</th>";
  }
  for ($i = 1; $i <= $fields7v7; $i++) {
    $html .= "<th>Campo 7v7 - " . esc_html($i) . "</th>";
  }
  return $html;
}

function generate_cell_info($match) {
  $html = "";
  $team1_name = "TBD";
  $team2_name = "TBD";

  if ($match->team_id_1) {
    $team1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
  }

  if ($match->team_id_2) {
    $team2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
  }

  if ($team1_name === "TBD" ) {
    $team1_name = PendingMatchesDatabase::get_match_by_bracket_match_and_playoff($match->match_link_1, $match->bracket_id, $match->playoff_id)->bracket_match;
  }

  if ($team2_name === "TBD" ) {
    $team2_name = PendingMatchesDatabase::get_match_by_bracket_match_and_playoff($match->match_link_2, $match->bracket_id, $match->playoff_id)->bracket_match;
  }

  $division = DivisionsDatabase::get_division_by_id($match->division_id);

  $colors = [['aqua',"black"], ['lightgreen',"black"], ['blue',"black"], ['pink',"black"], ['gray',"black"], ['green',"black"], 
  ['lime',"black"], ['maroon',"black"], ['navy',"black"], ['olive',"black"], ['orange',"black"], ['purple',"white"], ['red',"black"], 
  ['silver',"black"], ['teal',"black"], ['white',"black"], ['yellow',"black"], ['fuchsia',"white"]];

  $color = $colors[$match->division_id % count($colors)];
  $playoff = "";
  if ($match->match_type == 2) {
    $playoff = "<span style='font-weight: bold;'>ID: " . esc_html($match->bracket_match) . "</span>";
    $playoff .= "<span>Playoff - " . esc_html($match->playoff_id) . "</span>";
  }

  $html .= "<td style='background-color: $color[0]; color: $color[1];'>
    <div>"
        . $playoff .
        "<span>" . esc_html($division->division_name) . "</span>
        <span>" . esc_html($team1_name) . " vs " . esc_html($team2_name) . "</span>
      </div>
    </td>";

  return $html;
}

function generate_matches_rows($tournament) {
  $html = "";
  $days = explode(",", $tournament->tournament_days);

  $matches = PendingMatchesDatabase::get_matches_by_tournament($tournament->tournament_id);

  $fields5v5 = $tournament->tournament_fields_5v5;
  $fields7v7 = $tournament->tournament_fields_7v7;

  foreach ($days as $day) {
    $matches_this_day = array_filter($matches, function($match) use ($day) {
      return $match->match_date === $day;
    });

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
    $html .= "<tr>";
    $html .= "<td colspan='$colspan' style='text-align: center;'>" . esc_html(strtoupper($formatter->format($date))) . "</td>";
    $html .= "</tr>";

    for ($i = 7; $i <= 23; $i++) {
      $html .= "<tr>";
      $html .= "<td style='text-align: center;'>" . esc_html($i) . ":00</td>";

      $matches_this_hour = array_filter($matches_this_day, function($match) use ($i) {
        return $match->match_time == $i;
      });

      for ($j = 1; $j <= $fields5v5; $j++) {
        $match = array_find($matches_this_hour, function($match) use ($j) {
          return $match->field_number == $j && $match->field_type == 1;
        });
        if (!$match) {
          $html .= "<td></td>";
          continue;
        }
        $html .= generate_cell_info($match);
      }
      for ($j = 1; $j <= $fields7v7; $j++) {
        $match = array_find($matches_this_hour, function($match) use ($j) {
          return $match->field_number == $j && $match->field_type == 2;
        });
        if (!$match) {
          $html .= "<td></td>";
          continue;
        }
        $html .= generate_cell_info($match);
      }
      $html .= "</tr>";
    }
  }
  return $html;
} 

function create_table_for_matches_schedule($tournament) {
  if (is_null($tournament)) {
    return "<h3>No hay torneos activos</h3>";
  }
  $html = "<table id='matches-schedule-table' border='1' align='center'>";
  $html .= "<thead>";
  $html .= "<th>Horas\Campos</th>";
  $html .= generate_field_columns($tournament);
  $html .= "</thead>";
  $html .= "<tbody>";
  $html .= generate_matches_rows($tournament);
  $html .= "</tbody>";
  $html .= "</table>";
  return $html;
}

function matches_schedule_viewer() {
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
function enqueue_matches_schedule_scripts() {
	wp_enqueue_style( 'matches-schedule-styles', plugins_url('/styles.css', __FILE__) );
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