<?php

function render_official_match($match)
{
    $team_1_name = "TBD";
    $team_2_name = "TBD";

    if ($match->team_id_1) {
        $team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
    }

    if ($match->team_id_2) {
        $team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
    }

    $official_name = OfficialsDatabase::get_official_by_id($match->official_id)->official_name;

    $match_time = $match->match_time . ":00";
    $match_type = $match->match_type == 1 ? "Partido para puntos" : "Partido de playoffs";

    $html = "<div style='text-align: center;'>";
    $html .= "<span style='font-weight: bold; font-size: 18px;'>Tipo de partido: " . $match_type . "</span>";
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

function render_official_matches($tournament_id)
{
    $user_id = get_current_user_id();
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);
    $official = OfficialsDatabase::get_official_by_tournament_and_official_user_id($tournament_id, $user_id);
    $matches = PendingMatchesDatabase::get_matches_by_official($official->official_id);

    $html = "<div class='info-header'>
                <span id='back-button' data-screen='official-matches'>Volver</span>
                <h2 style='text-align: center; margin: 0;'>Mis Partidos</h2>
                </div>";
    $html .= "<h3 style='text-align: center; margin: 0;'>" . esc_html($tournament->tournament_name) . "</h3>";
    $html .= "<div class='official-matches'>";

    if (empty($matches)) {
        $html .= "<h3 style='text-align: center;'>No tienes partidos asignados para este torneo todavia.</h3>";
        $html .= "</div>";
        return $html;
    }

    foreach ($matches as $match) {
        $html .= "<div class='bracket-match-container'>";
        $html .= render_official_match($match);
        $html .= "</div>";
    }

    $html .= "</div>";
    return $html;
}

function render_official_tournaments()
{
    $tournaments = TournamentsDatabase::get_active_tournaments_not_started();
    $user_id = get_current_user_id();

    $html = "<h2 style='text-align: center;'>Torneos</h2>";
    $html .= "<div class='active-tournaments'>";

    $has_tournaments = false;
    foreach ($tournaments as $tournament) {
        // check if official is already registered or official is already in the tournament
        $pending_registration = OfficialsRegisterQueueDatabase::get_official_registration_by_tournament_and_official_id($tournament->tournament_id, $user_id);
        $official = OfficialsDatabase::get_official_by_tournament_and_official_user_id($tournament->tournament_id, $user_id);

        if (!empty($pending_registration) || !empty($official)) {
            continue;
        }

        $html .= "<div id='active-tournament' class='tournament-item-fe'>";
        $html .= "<span>" . esc_html($tournament->tournament_name) . "</span>";
        $html .= "<button id='join-tournament-official-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Registrarme</button>";
        $html .= "</div>";
        $has_tournaments = true;
    }

    if (empty($tournaments) || !$has_tournaments) {
        $html .= "<h3 style='text-align: center;'>No hay torneos activos para registrarte o ya estas registrado en un torneo</h3>";
    }

    $html .= "</div>";
    return $html;
}

function render_official_tournaments_for_matches()
{
    $tournaments = TournamentsDatabase::get_active_tournaments();
    $user_id = get_current_user_id();

    $html = "<h2 style='text-align: center;'>Torneos</h2>";
    $html .= "<div class='active-tournaments'>";

    $has_tournaments = false;
    foreach ($tournaments as $tournament) {
        // check if official is already registered in the tournament
        $official = OfficialsDatabase::get_official_by_tournament_and_official_user_id($tournament->tournament_id, $user_id);

        if (empty($official)) {
            continue;
        }

        $html .= "<div id='active-tournament' class='tournament-item-fe'>";
        $html .= "<span>" . esc_html($tournament->tournament_name) . "</span>";
        $html .= "<button id='show-tournament-official-matches-button' data-tournament-id='" . esc_attr($tournament->tournament_id) . "'>Ver mis partidos</button>";
        $html .= "</div>";
        $has_tournaments = true;
    }

    if (empty($tournaments) || !$has_tournaments) {
        $html .= "<h3 style='text-align: center;'>No estas registrado en torneos activos.</h3>";
    }

    $html .= "</div>";
    return $html;
}

function create_hours_select_input_official($tournament_hours)
{
    $html = "";
    foreach ($tournament_hours as $index => $hour) {
        $hours_start = intval($hour->tournament_hours_start);
        $hours_end = intval($hour->tournament_hours_end);
        $day = str_replace("/", "-", $hour->tournament_day);
        $html .= "<div class='hours-selector-container-official'>";
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
        $html .= "</div>";
    }
    return $html;
}

function render_tournament_info(int $tournament_id)
{
    $tournament = TournamentsDatabase::get_tournament_by_id($tournament_id);

    $days = $tournament->tournament_days;
    $tournament_hours = [];
    if (empty($tournament)) {
        $tournament_hours = [];
    } else {
        $tournament_hours = TournamentHoursDatabase::get_tournament_hours_by_tournament($tournament_id);
    }

    $html = "<div class='info-header'>
                <span id='back-button' data-screen='official-tournaments'>Volver</span>
                <h2 style='text-align: center; margin: 0;'>" . esc_html($tournament->tournament_name) . "</h2>
            </div>";

    $html .= "<div class='tournament-info'>";
    $html .= "<p style='text-align: center; margin-bottom: 1rem; font-size: 20px;'>Completa el formulario para registrar tu participacion como arbitro en el torneo</p>";
    $html .= "<form id='join-tournament-official-form' class='official-form' data-tournament-id='$tournament_id'>";

    // tournament days
    $html .= "<div class='form-group'>";
    $html .= "<label class='form-label' id='tournament-selected-days' data-tournament-days='$days'>Dias del torneo: </label>";
    $html .= "<div>";
    $html .= create_hours_select_input_official($tournament_hours);
    $html .= "</div>";
    $html .= "</div>";

    // modes 
    $html .= "<div class='form-group'>";
    $html .= "<label for='match_type' class='form-label'>Modalidad: </label>";
    $html .= "<div class='form-radio-group'>";
    $html .= "<div class='form-radio'>";
    $html .= "<input name='match_type' class='form-input' type='radio' value='1' checked required/>";
    $html .= "<label for='match_type'>5v5</label>";
    $html .= "</div>";
    $html .= "<div class='form-radio'>";
    $html .= "<input name='match_type' class='form-input' type='radio' value='2' required/>";
    $html .= "<label for='match_type'>7v7</label>";
    $html .= "</div>";
    $html .= "<div class='form-radio'>";
    $html .= "<input name='match_type' class='form-input' type='radio' value='3' required/>";
    $html .= "<label for='match_type'>Ambas</label>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";

    $html .= "<button type='submit'>Registrarme</button>";
    $html .= "</form>";
    $html .= "</div>";
    return $html;
}

function render_join_tournament_official()
{
    $tournament_id = $_POST["tournament_id"];
    wp_send_json_success(array('message' => 'Torneo mostrado para arbitro', 'html' => render_tournament_info($tournament_id)));
}

function handle_join_tournament_official()
{
    if (!isset($_POST["tournament_id"]) || !isset($_POST["official_mode"]) || !isset($_POST["official_hours"]) || !isset($_POST["official_days"])) {
        wp_send_json_error(array('message' => 'Faltan Datos'));
    }

    $user_id = get_current_user_id();
    $official = OfficialsUserDatabase::get_official_by_id($user_id);

    $tournament_id = intval($_POST["tournament_id"]);
    $mode = intval($_POST["official_mode"]);
    $official_hours = $_POST["official_hours"];
    $schedule = $_POST["official_days"];
    $name = $official->user_name;
    $contact = $official->user_contact;
    $city = $official->user_city;
    $state = $official->user_state;
    $country = $official->user_country;

    $result = OfficialsRegisterQueueDatabase::insert_official(
        $tournament_id,
        $user_id,
        $name,
        $contact,
        $schedule,
        $mode,
        $city,
        $state,
        $country
    );

    if ($result[0]) {
        $official = OfficialsRegisterQueueDatabase::get_officials_register_queue_by_id($result[1]);

        foreach ($official_hours as $day => $hours) {
            $hours_str = implode(",", $hours);
            OfficialsRegisterHoursDatabase::insert_official_hours($official->official_register_queue_id, $day, $hours_str);
        }
        wp_send_json_success(array('message' => 'Arbitro registrado correctamente', 'html' => render_official_tournaments()));
    }

    wp_send_json_error(array(
        'message' => 'Error al registrar arbitro',
        'tournament_id' => $tournament_id,
        'mode' => $mode,
        'official_hours' => $official_hours,
        'schedule' => $schedule,
        'name' => $name,
        'contact' => $contact,
        'city' => $city,
        'state' => $state,
        'country' => $country
    ));
}


function handle_render_official_matches_fe()
{
    if (!isset($_POST["tournament_id"])) {
        wp_send_json_error(array('message' => 'Faltan Datos'));
    }
    $tournament_id = intval($_POST["tournament_id"]);
    wp_send_json_success(array('message' => 'Partidos mostrados para arbitro', 'html' => render_official_matches($tournament_id)));
}

add_action("wp_ajax_handle_render_official_matches_fe", "handle_render_official_matches_fe");
add_action("wp_ajax_nopriv_handle_render_official_matches_fe", "handle_render_official_matches_fe");
add_action("wp_ajax_render_join_tournament_official", "render_join_tournament_official");
add_action("wp_ajax_nopriv_render_join_tournament_official", "render_join_tournament_official");
add_action("wp_ajax_handle_join_tournament_official", "handle_join_tournament_official");
add_action("wp_ajax_nopriv_handle_join_tournament_official", "handle_join_tournament_official");
