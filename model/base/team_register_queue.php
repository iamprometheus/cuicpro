<?php
declare(strict_types=1);

Class TeamRegisterQueueDatabase {
    public static function init() {
        self::create_team_register_queue_table();
    }

    public static function create_team_register_queue_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_team_register_queue'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_team_register_queue (
            team_register_queue_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            division_id SMALLINT UNSIGNED NOT NULL,
            coach_id SMALLINT UNSIGNED NOT NULL,
            team_id SMALLINT UNSIGNED NOT NULL,
            PRIMARY KEY (team_register_queue_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
            FOREIGN KEY (division_id) REFERENCES {$wpdb->prefix}cuicpro_divisions(division_id),
            FOREIGN KEY (coach_id) REFERENCES {$wpdb->prefix}cuicpro_coaches_user(user_id),
            FOREIGN KEY (team_id) REFERENCES {$wpdb->prefix}cuicpro_teams_user(team_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_team_register_queue() {
        global $wpdb;
        $team_register_queue = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue" );
        return $team_register_queue;
    }

    public static function get_team_register_queue_by_id(int $team_register_queue_id) {
        global $wpdb;
        $team_register_queue = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue WHERE team_register_queue_id = $team_register_queue_id" );
        return $team_register_queue;
    }

    public static function get_teams_by_tournament(int $tournament_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue WHERE tournament_id = $tournament_id" );
        return $teams;
    }

    public static function get_teams_by_division(int $division_id) {
      global $wpdb;
      $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue WHERE division_id = $division_id" );
      return $teams;
    }

    public static function get_teams_by_tournament_and_division(int $tournament_id, int $division_id) {
      global $wpdb;
      $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue WHERE tournament_id = $tournament_id AND division_id = $division_id" );
      return $teams;
    }

    public static function get_teams_by_coach(int $coach_id) {
      global $wpdb;
      $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue WHERE coach_id = $coach_id" );
      return $teams;
    }

    public static function is_team_pending($team_id) {
      global $wpdb;
      $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_team_register_queue WHERE team_id = $team_id" );
      if ($team) {
        return true;
      }
      return false;
    }

    public static function insert_team(
      int $tournament_id,
      int $division_id,
      int $coach_id,
      int $team_id
       ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_team_register_queue',
            array(
                'tournament_id' => $tournament_id,
                'division_id' => $division_id,
                'coach_id' => $coach_id,
                'team_id' => $team_id,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function delete_team(int $team_register_queue_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_team_register_queue',
            array(
                'team_register_queue_id' => $team_register_queue_id,
            )
        );
        if ( $result ) {
            return "Team deleted successfully";
        }
        return "Team not deleted or team not found";
    }
}
