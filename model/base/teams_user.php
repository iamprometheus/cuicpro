<?php
declare(strict_types=1);

Class TeamsUserDatabase {
    public static function init() {
        self::create_teams_user_table();
    }

    public static function create_teams_user_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_teams_user'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_teams_user (
            team_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            team_name VARCHAR(255) NOT NULL,
            team_logo VARCHAR(255) NOT NULL,
            coach_id SMALLINT UNSIGNED NOT NULL,
            is_registered BOOLEAN NOT NULL DEFAULT FALSE,
            PRIMARY KEY (team_id),
            FOREIGN KEY (coach_id) REFERENCES {$wpdb->prefix}cuicpro_coaches_user(coach_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_teams() {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams_user" );
        return $teams;
    }

    public static function get_teams_by_coach(int $coach_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams_user WHERE coach_id = $coach_id" );
        return $teams;
    }

    public static function get_team_by_id(int $team_id) {
        global $wpdb;
        $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams_user WHERE team_id = $team_id" );
        return $team;
    }

    public static function get_team_by_name(string $team_name, int $coach_id) {
        global $wpdb;

        $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams_user WHERE team_name = '$team_name' AND coach_id = $coach_id" );
        return $team;
    }

    public static function insert_team(string $team_name, int $coach_id, string $logo ) {
        
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_teams_user',
            array(
                'team_name' => $team_name,
                'coach_id' => $coach_id,
                'team_logo' => $logo,
                'is_registered' => false,
                )
            );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_team(int $team_id, string $team_name, string $logo ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams_user',
            array(
                'team_name' => $team_name,
                'team_logo' => $logo,
            ),
            array(
                'team_id' => $team_id,
            )
        );
        if ( $result ) {
            return "Team updated successfully";
        }
        return "Team not updated";
    }

    public static function update_team_is_registered(int $team_id, bool $is_registered ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams_user',
            array(
                'is_registered' => $is_registered,
            ),
            array(
                'team_id' => $team_id,
            )
        );
        if ( $result ) {
            return "Team updated successfully";
        }
        return "Team not updated";
    }

    public static function delete_team(int $team_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_teams_user',
            array(
                'team_id' => $team_id,
            )
        );
        if ( $result ) {
            return "Team deleted successfully";
        }
        return "Team not deleted or team not found";
    }
}
