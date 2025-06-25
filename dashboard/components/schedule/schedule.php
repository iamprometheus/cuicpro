<?php

function generate_day_columns($tournament) {
  $html = "";
  $days = explode(",", $tournament->tournament_days);
  foreach ($days as $day) {
    $html .= "<th>" . esc_html($day) . "</th>";
  }
  return $html;
}

function generate_time_rows($tournament) {
  $tournament_hours = TournamentHoursDatabase::get_tournament_hours_by_tournament($tournament->tournament_id);
  $html = "";
  $hours = $tournament_hours;
  $days = explode(",", $tournament->tournament_days);

  $officials = OfficialsDatabase::get_officials_by_tournament($tournament->tournament_id);

  foreach ($officials as $official) {
    $official_hours = OfficialsHoursDatabase::get_official_hours($official->official_id);

    $official->official_hours = [];
    foreach ($official_hours as $official_hour) {
      $official->official_hours[$official_hour->official_day] = $official_hour->official_hours;
    }
  }

  for ($i = 7; $i <= 23; $i++) {
    $html .= "<tr>";
    $html .= "<td style='text-align: center;'>" . esc_html($i) . ":00</td>";

    foreach ($days as $day) {
      $hours_this_day = array_find($hours, function($hour) use ($day) {
        return $hour->tournament_day === $day;
      });

      if ($i >= intval($hours_this_day->tournament_hours_start) && $i <= intval($hours_this_day->tournament_hours_end)) {
        $matches = count(PendingMatchesDatabase::get_matches_by_date($i, $day, $tournament->tournament_id));
        $officials_this_day = array_filter($officials, function($official) use ($i,$day) {
          return str_contains($official->official_schedule, $day) && str_contains($official->official_hours[$day], strval($i));
        });

        $officials_this_hour = array_map(function($official) {
          return ['name' => $official->official_name, 'id' => $official->official_id];
        }, $officials_this_day);

        $officials_count = count($officials_this_hour);

        $officials_html = "";
        foreach ($officials_this_hour as $official) {
          $official_hours = OfficialsHoursDatabase::get_official_hours_by_day($official['id'], $day)->official_available_hours;
          $is_drafted = !str_contains($official_hours, strval($i));
          $officials_html .= "<span style='text-align: center;'>" . esc_html($official['name']) . " " . ($is_drafted ? "&check;" : "&times;") . "</span>";
        }

        $color = "lightgreen";
        if ($matches > $officials_count) {
          $color = "orange";
        }

        $html .= "<td style='background-color: $color;'><div><span>#Partidos: " . $matches . "</span><div>" . $officials_html . "</div></div></td>";
      } else {
        $html .= "<td style='background-color: darkgray;'></td>";
      }
    }
    $html .= "</tr>";
  }
  return $html;
}

function create_table_for_schedule($tournament) {
  if (is_null($tournament)) {
    return "<h3>No hay torneos activos</h3>";
  }
  $html = "<table id='schedule-table' border='1' align='center'>";
  $html .= "<thead>";
  $html .= "<th>Horas:</th>";
  $html .= generate_day_columns($tournament);
  $html .= "</thead>";
  $html .= "<tbody>";
  $html .= generate_time_rows($tournament);
  $html .= "</tbody>";
  $html .= "</table>";
  return $html;
}

function schedule_viewer() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $tournament = null;
  if (!empty($tournaments)) {
    $tournament = $tournaments[0];
  }

  $html = "";
  $html .= "<div id='schedule-container'>";
  $html .= create_table_for_schedule($tournament);
  $html .= "</div>";
  echo $html;
}

// enqueue scripts related to this file
function enqueue_schedule_scripts() {
	wp_enqueue_style( 'schedule-styles', plugins_url('/styles.css', __FILE__) );
	wp_enqueue_script(
			'schedule-script',
			plugins_url('/handle_schedule_request.js', __FILE__),
			array('jquery'),
			null,
			true
	);

	// Pass the AJAX URL to JavaScript
	wp_localize_script('schedule-script', 'cuicpro', array(
			'ajax_url' => admin_url('admin-ajax.php')
	));
}
add_action('admin_enqueue_scripts', 'enqueue_schedule_scripts');

require_once __DIR__ . '/handle_schedule_request.php';