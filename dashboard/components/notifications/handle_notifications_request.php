<?php

function handle_create_notification_template()
{
    if (!isset($_POST['notification_title']) || !isset($_POST['notification_message']) || !isset($_POST['notification_id'])) {
        return wp_send_json_error(['message' => 'Faltan datos']);
    }
    $notification_title = sanitize_text_field($_POST['notification_title']);
    $notification_message = sanitize_textarea_field($_POST['notification_message']);
    $notification_id = intval($_POST['notification_id']);

    if ($notification_id == 0) {
        $result = NotificationsDatabase::insert_notification($notification_title, $notification_message, 'custom');
        if ($result[0]) {
            return wp_send_json_success(['message' => 'Notificación creada correctamente', 'notification_id' => $result[1]]);
        }
        return wp_send_json_error(['message' => 'Error al crear la notificación']);
    }

    NotificationsDatabase::update_notification($notification_id, $notification_title, $notification_message);
    return wp_send_json_success(['message' => 'Notificación actualizada correctamente']);
}

function handle_delete_notification_template()
{
    if (!isset($_POST['notification_id'])) {
        return wp_send_json_error(['message' => 'Faltan datos']);
    }
    $notification_id = intval($_POST['notification_id']);
    NotificationsDatabase::delete_notification($notification_id);
    $notification_initial = NotificationsDatabase::get_notification(1);
    return wp_send_json_success(['message' => 'Notificación eliminada correctamente', 'notification_message' => $notification_initial->notification_message]);
}

function handle_send_notification()
{
    if (!isset($_POST['user_type']) || !isset($_POST['registered']) || !isset($_POST['tournament']) || !isset($_POST['type']) || !isset($_POST['message'])) {
        return wp_send_json_error(['message' => 'Faltan datos']);
    }
    $user_type = intval($_POST['user_type']);
    $registered = intval($_POST['registered']);
    $tournament = intval($_POST['tournament']);
    $type = intval($_POST['type']);
    $message = sanitize_textarea_field($_POST['message']);
    $emails_sent = [];

    $notifications = NotificationsDatabase::get_system_notifications();
    $notification_type_1 = $notifications[0]->notification_id;
    $notification_type_2 = $notifications[1]->notification_id;
    $notification_type_3 = $notifications[2]->notification_id;

    // if registered is 1 (tournament), send to users registered in tournament
    if ($registered == 1) {
        if ($type == $notification_type_1 || $type == $notification_type_2) {
            // determine which type of notification it is (1 = pending matches today, 2 = pending matches tomorrow)
            $matches = [];
            $matches = PendingMatchesDatabase::get_pending_matches_by_tournament($tournament);

            $current_day = "";
            $subject = "";
            if ($type == $notification_type_1) {
                $current_day = date("j/m/y");
                $subject = "Partidos para el dia de hoy";
            } else if ($type == $notification_type_2) {
                $current_day = date("j/m/y", strtotime("+1 day"));
                $subject = "Partidos para el dia de mañana";
            }

            $matches = array_filter($matches, function ($match) use ($current_day) {
                return $match->match_date == $current_day;
            });

            foreach ($matches as $match) {
                // obtain coaches from teams in match that have account registered
                $team1 = null;
                $team1_name = "Por definir";
                if ($match->team_id_1) {
                    $team1 = TeamsDatabase::get_team_by_id($match->team_id_1);
                    $team1_name = $team1->team_name;
                }

                $team2 = null;
                $team2_name = "Por definir";
                if ($match->team_id_2) {
                    $team2 = TeamsDatabase::get_team_by_id($match->team_id_2);
                    $team2_name = $team2->team_name;
                }

                $official = null;
                if ($match->official_id) {
                    $official = OfficialsDatabase::get_official_by_id($match->official_id);
                }

                if ($team1) {
                    // check for coach and players from this team
                    $coach = CoachesDatabase::get_coach_by_id($team1->coach_id);

                    // if coach has account registered and user type is coach or all, send notification
                    if (($user_type == 0 || $user_type == 1) && $coach->coach_user_id) {
                        notification_coach($subject, $match, $coach, $official, $message, $emails_sent);
                    }


                    // check for players from this team
                    // if user type is players or all, send notification
                    if ($user_type == 0 || $user_type == 2) {
                        $players = PlayersDatabase::get_players_by_team($team1->team_id);
                        foreach ($players as $player) {
                            if ($player->player_user_id) {
                                notification_player($subject, $match, $player, $official, $message, $emails_sent);
                            }
                        }
                    }

                    // check for officials
                    // if user type is officials or all, send notification
                    if (($user_type == 0 || $user_type == 3) && $official && $official->official_user_id) {
                        notification_official($subject, $match, $official, $message, $emails_sent);
                    }
                }

                if ($team2) {
                    $coach = CoachesDatabase::get_coach_by_id($team2->coach_id);

                    // if coach has account registered, send notification
                    if (($user_type == 0 || $user_type == 1) && $coach->coach_user_id) {
                        notification_coach($subject, $match, $coach, $official, $message, $emails_sent);
                    }

                    // check for players from this team
                    // if user type is players or all, send notification
                    if ($user_type == 0 || $user_type == 2) {
                        $players = PlayersDatabase::get_players_by_team($team2->team_id);
                        foreach ($players as $player) {
                            if ($player->player_user_id) {
                                notification_player($subject, $match, $player, $official, $message, $emails_sent);
                            }
                        }
                    }

                    // check for officials
                    // if user type is officials or all, send notification
                    if (($user_type == 0 || $user_type == 3) && $official && $official->official_user_id) {
                        notification_official($subject, $match, $official, $message, $emails_sent);
                    }
                }
            }
        } else if ($type == $notification_type_3) {
            $matches = [];
            $matches = PendingMatchesDatabase::get_pending_matches_by_tournament($tournament);
            $subject = "Horarios de partidos disponibles";

            foreach ($matches as $match) {
                // obtain coaches from teams in match that have account registered
                $team1 = null;
                $team1_name = "Por definir";
                if ($match->team_id_1) {
                    $team1 = TeamsDatabase::get_team_by_id($match->team_id_1);
                    $team1_name = $team1->team_name;
                }

                $team2 = null;
                $team2_name = "Por definir";
                if ($match->team_id_2) {
                    $team2 = TeamsDatabase::get_team_by_id($match->team_id_2);
                    $team2_name = $team2->team_name;
                }

                $official = null;
                if ($match->official_id) {
                    $official = OfficialsDatabase::get_official_by_id($match->official_id);
                }

                if ($team1) {
                    // check for coach and players from this team
                    $coach = CoachesDatabase::get_coach_by_id($team1->coach_id);

                    // if coach has account registered and user type is coach or all, send notification
                    if (($user_type == 0 || $user_type == 1) && $coach->coach_user_id) {
                        notification_coach($subject, $match, $coach, $official, $message, $emails_sent);
                    }


                    // check for players from this team
                    // if user type is players or all, send notification
                    if ($user_type == 0 || $user_type == 2) {
                        $players = PlayersDatabase::get_players_by_team($team1->team_id);
                        foreach ($players as $player) {
                            if ($player->player_user_id) {
                                notification_player($subject, $match, $player, $official, $message, $emails_sent);
                            }
                        }
                    }

                    // check for officials
                    // if user type is officials or all, send notification
                    if (($user_type == 0 || $user_type == 3) && $official && $official->official_user_id) {
                        notification_official($subject, $match, $official, $message, $emails_sent);
                    }
                }

                if ($team2) {
                    $coach = CoachesDatabase::get_coach_by_id($team2->coach_id);

                    // if coach has account registered, send notification
                    if (($user_type == 0 || $user_type == 1) && $coach->coach_user_id) {
                        notification_coach($subject, $match, $coach, $official, $message, $emails_sent);
                    }

                    // check for players from this team
                    // if user type is players or all, send notification
                    if ($user_type == 0 || $user_type == 2) {
                        $players = PlayersDatabase::get_players_by_team($team2->team_id);
                        foreach ($players as $player) {
                            if ($player->player_user_id) {
                                notification_player($subject, $match, $player, $official, $message, $emails_sent);
                            }
                        }
                    }

                    // check for officials
                    // if user type is officials or all, send notification
                    if (($user_type == 0 || $user_type == 3) && $official && $official->official_user_id) {
                        notification_official($subject, $match, $official, $message, $emails_sent);
                    }
                }
            }
        } else {
        }

        return wp_send_json_success([
            'message' => 'Notificación enviada correctamente',
            'emails_sent' => $emails_sent,
        ]);
    }
}

function handle_switch_notification_template()
{
    if (!isset($_POST['notification_id'])) {
        return wp_send_json_error(['message' => 'Faltan datos']);
    }
    $notification_id = $_POST['notification_id'];
    $notification = NotificationsDatabase::get_notification($notification_id);
    return wp_send_json_success(['message' => 'Notificación cambiada correctamente', 'notification_message' => $notification->notification_message]);
}

function notification_coach($subject, $match, $coach, $official, $message, &$emails_sent)
{
    $user = get_userdata($coach->coach_user_id);
    if ($user && !in_array($user->user_email, $emails_sent)) {
        $to = $user->user_email;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message = str_replace("[name]", $user->display_name, $message);
        $message = str_replace("[date]", $match->match_date, $message);
        $message = str_replace("[time]", $match->match_time, $message);
        $message = str_replace("[field]", $match->field_number, $message);
        $message = str_replace("[official]", $official->official_name, $message);
        wp_mail($to, $subject, $message, $headers);
        $emails_sent[] = $to;
    }
}

function notification_player($subject, $match, $player, $official, $message, &$emails_sent)
{
    $user = get_userdata($player->player_user_id);
    if ($user && !in_array($user->user_email, $emails_sent)) {
        $to = $user->user_email;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message = str_replace("[name]", $user->display_name, $message);
        $message = str_replace("[date]", $match->match_date, $message);
        $message = str_replace("[time]", $match->match_time, $message);
        $message = str_replace("[field]", $match->field_number, $message);
        $message = str_replace("[official]", $official->official_name, $message);
        wp_mail($to, $subject, $message, $headers);
        $emails_sent[] = $to;
    }
}

function notification_official($subject, $match, $official, $message, &$emails_sent)
{
    $user = get_userdata($official->official_user_id);
    if ($user && !in_array($user->user_email, $emails_sent)) {
        $official_user = OfficialsUserDatabase::get_official_by_id($official->official_user_id);
        $to = $user->user_email;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message = str_replace("[name]", $official_user->user_name, $message);
        $message = str_replace("[date]", $match->match_date, $message);
        $message = str_replace("[time]", $match->match_time, $message);
        $message = str_replace("[field]", $match->field_number, $message);
        $message = str_replace("[official]", $official->official_name, $message);
        wp_mail($to, $subject, $message, $headers);
        $emails_sent[] = $to;
    }
}

add_action('wp_ajax_handle_create_notification_template', 'handle_create_notification_template');
add_action('wp_ajax_nopriv_handle_create_notification_template', 'handle_create_notification_template');
add_action('wp_ajax_handle_delete_notification_template', 'handle_delete_notification_template');
add_action('wp_ajax_nopriv_handle_delete_notification_template', 'handle_delete_notification_template');
add_action('wp_ajax_handle_send_notification', 'handle_send_notification');
add_action('wp_ajax_nopriv_handle_send_notification', 'handle_send_notification');
add_action('wp_ajax_handle_switch_notification_template', 'handle_switch_notification_template');
add_action('wp_ajax_nopriv_handle_switch_notification_template', 'handle_switch_notification_template');
