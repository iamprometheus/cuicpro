<?php

// function render_divisions_dropdown($tournament_id) {
//   $html = "";
//   if ($tournament_id == null) {
//     return $html;
//   }
  
//   $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
//   foreach ($divisions as $division) {
//     $html .= "<option value='".$division->division_id."'>".$division->division_name."</option>";
//   }
//   return $html;
// }

// function handle_register_form() {
//     $coach_name = sanitize_text_field($_POST['name']);
//     $coach_last_name = sanitize_text_field($_POST['last_name']);
//     $coach_contact = sanitize_text_field($_POST['phone']);
//     $coach_city = sanitize_text_field($_POST['city']);
//     $coach_state = sanitize_text_field($_POST['state']);
//     $coach_country = sanitize_text_field($_POST['country']);
//     $team_name = sanitize_text_field($_POST['team_name']);
//     $logo = $_FILES['logo'];
//     $division_id = intval($_POST['division']);
//     $tournament_id = intval($_POST['tournament']);
//     $players_count = intval($_POST['players_count']);

//     $coach_full_name = $coach_name . " " . $coach_last_name;
//     // upload image to wordpress and get the attachment id to link to team
//     $attachment_id = null;
//     addImageToWordPressMediaLibrary($logo['tmp_name'], $logo['name'], $logo['name'], $attachment_id);

//     $result= TeamRegisterQueueDatabase::insert_team(
//       $tournament_id, 
//       $division_id, 
//       $coach_full_name, 
//       $coach_contact, 
//       $coach_city, 
//       $coach_state, 
//       $coach_country, 
//       $team_name, 
//       $attachment_id
//     );
    
//     if ($result[0]) {
//       for ($i = 0; $i < $players_count; $i++) {
//         $player_logo = $_FILES['player_logo_' . $i];
//         $player_name = sanitize_text_field($_POST['player_name_' . $i]);
//         $player_last_name = sanitize_text_field($_POST['player_last_name_' . $i]);
//         $attachment_id = null;
//         addImageToWordPressMediaLibrary($player_logo['tmp_name'], $player_logo['name'], $player_logo['name'], $attachment_id);

//         $player_full_name = $player_name . " " . $player_last_name;
//         PendingPlayersDatabase::insert_player(
//           $result[1], 
//           $player_full_name, 
//           $attachment_id
//         );
//       }
//         wp_send_json_success(array('message' => 'Team registered successfully!', 'id' => $result[1]));
//     } 
//     wp_send_json_error(array('message' => 'Team not registered'));
// }

// function fetch_tournament_divisions() {
//     if (!isset($_POST['tournament_id'])) {
//         wp_send_json_error(array('message' => 'Tournament ID is required!'));
//     }
    
//     // Your PHP logic here
//     $tournament_id = $_POST['tournament_id'];
//     $html = render_divisions_dropdown($tournament_id);

//     wp_send_json_success(array('message' => 'Divisions fetched successfully!', 'html' => $html));
// }

function user_logged_in() {
    if (is_user_logged_in()) {
        wp_send_json_success(array('message' => 'User is logged in!'));
    } else {
        wp_send_json_error(array('message' => 'User is not logged in!'));
    }
}

add_action('wp_ajax_user_logged_in', 'user_logged_in');
add_action('wp_ajax_nopriv_user_logged_in', 'user_logged_in');
// add_action('wp_ajax_handle_register_form', 'handle_register_form');
// add_action('wp_ajax_nopriv_handle_register_form', 'handle_register_form'); // for non-logged-in users
// add_action('wp_ajax_fetch_tournament_divisions', 'fetch_tournament_divisions');
// add_action('wp_ajax_nopriv_fetch_tournament_divisions', 'fetch_tournament_divisions');
