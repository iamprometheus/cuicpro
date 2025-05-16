<?php
declare(strict_types=1);

Class TeamsDatabase {
    public static function init() {
        self::create_teams_table();
    }

    public static function create_teams_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_teams'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_teams (
            team_id SMALLINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
            league_id SMALLINT(2) UNSIGNED NOT NULL,
            team_name VARCHAR(255) NOT NULL,
            city VARCHAR(255) NOT NULL,
            state VARCHAR(255) NOT NULL,
            country VARCHAR(255) NOT NULL,
            coach_id SMALLINT(2) UNSIGNED NOT NULL,
            logo VARCHAR(255) NOT NULL,
            PRIMARY KEY (team_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_teams() {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams" );
        return $teams;
    }
    
    public static function get_teams_by_league(int $league_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE league_id = $league_id" );
        return $teams;
    }

    public static function get_teams_by_coach(int $coach_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE coach_id = $coach_id" );
        return $teams;
    }

    public static function get_team_by_name(string $team_name, int $league_id) {
        global $wpdb;
        $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_name = '$team_name' AND league_id = $league_id" );
        return $team;
    }

    public static function insert_team(string $team_name, int $league_id, string $city, string $state, string $country, int $coach_id, string $logo ) {
        if ( self::get_team_by_name( $team_name, $league_id ) ) {
            return false;
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_name' => $team_name,
                'league_id' => $league_id,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'coach_id' => $coach_id,
                'logo' => $logo,
            )
        );

        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_team(int $team_id, string $team_name, int $league_id, string $city, string $state, string $country, int $coach_id, string $logo ) {
        if ( self::get_team_by_name( $team_name, $league_id ) ) {
            return "Team with this name already exists in this league";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_name' => $team_name,
                'league_id' => $league_id,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'coach_id' => $coach_id,
                'logo' => $logo,
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
            $wpdb->prefix . 'cuicpro_teams',
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
