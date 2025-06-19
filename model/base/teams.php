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
            team_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            division_id SMALLINT UNSIGNED NULL,
            team_name VARCHAR(255) NOT NULL,
            team_category TINYINT UNSIGNED NOT NULL,
            team_mode TINYINT UNSIGNED NOT NULL,
            coach_id SMALLINT UNSIGNED NOT NULL,
            logo VARCHAR(255) NOT NULL,
            is_enrolled BOOLEAN NOT NULL,
            team_visible BOOLEAN NOT NULL,
            team_points INT NOT NULL DEFAULT 0,
            PRIMARY KEY (team_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
            FOREIGN KEY (division_id) REFERENCES {$wpdb->prefix}cuicpro_divisions(division_id),
            FOREIGN KEY (team_category) REFERENCES {$wpdb->prefix}cuicpro_categories(category_id),
            FOREIGN KEY (team_mode) REFERENCES {$wpdb->prefix}cuicpro_modes(mode_id),
            FOREIGN KEY (coach_id) REFERENCES {$wpdb->prefix}cuicpro_coaches(coach_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_teams() {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_visible = true" );
        return $teams;
    }
    
    public static function get_teams_by_division(int $division_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE division_id = $division_id AND team_visible = true" );
        return $teams;
    }

    public static function get_enrolled_teams_by_division(int $division_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE division_id = $division_id AND is_enrolled = true AND team_visible = true" );
        return $teams;
    }

    public static function get_teams_by_coach(int $coach_id) {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE coach_id = $coach_id AND team_visible = true" );
        return $teams;
    }

    public static function get_team_by_id(int | null $team_id) {
        if ( $team_id === null ) {
            return null;
        }
        global $wpdb;
        $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_id = $team_id AND team_visible = true" );
        return $team;
    }

    public static function get_team_by_name(string $team_name, int | null $division_id) {
        global $wpdb;

        if ( $division_id === null ) {
            $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_name = '$team_name' AND division_id IS NULL AND team_visible = true" );
        } else {
            $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_name = '$team_name' AND division_id = $division_id AND team_visible = true" );
        }
        return $team;
    }

    public static function get_team_by_category(string $team_category) {
        global $wpdb;
        $team = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_category = '$team_category' AND team_visible = true" );
        return $team;
    }

    public static function insert_team(string $team_name, int $tournament_id, int | null $division_id, int $team_category, int $team_mode, int $coach_id, string $logo ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_name' => $team_name,
                'tournament_id' => $tournament_id,
                'division_id' => $division_id,
                'team_category' => $team_category,
                'team_mode' => $team_mode,
                'coach_id' => $coach_id,
                'logo' => $logo,
                'is_enrolled' => false,
                'team_visible' => true,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_team(int $team_id, string $team_name, int | null $division_id, int $team_category, int $team_mode, int $coach_id, string $logo, bool $visible ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_name' => $team_name,
                'division_id' => $division_id,
                'team_category' => $team_category,
                'team_mode' => $team_mode,
                'coach_id' => $coach_id,
                'logo' => $logo,
                'team_visible' => $visible,
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

    public static function update_team_enrolled(int $team_id, bool $is_enrolled ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'is_enrolled' => $is_enrolled,
            ),
            array(
                'team_id' => $team_id,
            )
        );
        if ( $result ) {
            return "Team enrolled successfully";
        }
        return "Team not enrolled or team not found";
    }

    public static function update_team_points(int $team_id, int $points ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_points' => $points,
            ),
            array(
                'team_id' => $team_id,
            )
        );
        if ( $result ) {
            return "Team points updated successfully";
        }
        return "Team points not updated or team not found";
    }

    public static function delete_team(int $team_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_visible' => false,
            ),
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
