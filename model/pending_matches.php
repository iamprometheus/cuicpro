<?php
declare(strict_types=1);

Class PendingMatchesDatabase {
    public static function init() {
        self::create_pending_matches_table();
    }

    public static function create_pending_matches_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_pending_matches'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_pending_matches (
            match_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            division_id SMALLINT UNSIGNED NOT NULL,
            bracket_id INT UNSIGNED NOT NULL,
            bracket_round SMALLINT UNSIGNED NOT NULL,
            bracket_match SMALLINT UNSIGNED NOT NULL,
            team_id_1 SMALLINT UNSIGNED NULL,
            team_id_2 SMALLINT UNSIGNED NULL,
            field_number TINYINT UNSIGNED NOT NULL,
            field_type TINYINT UNSIGNED NOT NULL,
            official_id SMALLINT UNSIGNED NULL,
            match_date VARCHAR(255) NOT NULL,
            match_time TINYINT UNSIGNED NOT NULL,
            goals_team_1 TINYINT UNSIGNED NULL,
            goals_team_2 TINYINT UNSIGNED NULL,
            match_link_1 MEDIUMINT UNSIGNED NULL,
            match_link_2 MEDIUMINT UNSIGNED NULL,
            match_type TINYINT UNSIGNED NOT NULL,
            playoff_id TINYINT UNSIGNED NULL,
            match_pending BOOLEAN NOT NULL,
            PRIMARY KEY (match_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
            FOREIGN KEY (division_id) REFERENCES {$wpdb->prefix}cuicpro_divisions(division_id),
            FOREIGN KEY (bracket_id) REFERENCES {$wpdb->prefix}cuicpro_brackets(bracket_id),
            FOREIGN KEY (team_id_1) REFERENCES {$wpdb->prefix}cuicpro_teams(team_id),
            FOREIGN KEY (team_id_2) REFERENCES {$wpdb->prefix}cuicpro_teams(team_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_matches() {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches" );
        return $matches;
    }

    public static function get_matches_by_tournament(int $tournament_id) {
      global $wpdb;
      $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE tournament_id = $tournament_id" );
      return $matches;
    }
    
    public static function get_matches_by_division(int $division_id, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE division_id = $division_id AND tournament_id = $tournament_id" );
        return $matches;
    }

    public static function get_matches_by_bracket(int $bracket_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE bracket_id = $bracket_id"  );
        return $matches;
    }

    public static function get_matches_by_date(int $match_time, string $match_date, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE match_date = '$match_date' AND match_time = $match_time AND tournament_id = $tournament_id"  );
        return $matches;
    }

    public static function get_pending_matches_by_team(int $team_id, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE (team_id_1 = $team_id OR team_id_2 = $team_id) AND tournament_id = $tournament_id AND match_pending = true"  );
        return $matches;
    }

    public static function get_pending_matches_by_bracket(int $bracket_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE bracket_id = $bracket_id AND match_pending = true"  );
        return $matches;
    }

    public static function get_pending_matches_by_tournament(int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE tournament_id = $tournament_id AND match_pending = true" );
        return $matches;
    }

    public static function get_matches_by_team(int $team_id, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE (team_id_1 = $team_id OR team_id_2 = $team_id) AND tournament_id = $tournament_id AND match_pending = true" );
        return $matches;
    }

    public static function get_all_matches_by_team(int $team_id, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE (team_id_1 = $team_id OR team_id_2 = $team_id) AND tournament_id = $tournament_id" );
        return $matches;
    }

    public static function get_match_by_id(int $match_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE match_id = $match_id" );
        return $match;
    }

    public static function get_match_by_bracket_match(int $bracket_match, int $bracket_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE bracket_match = $bracket_match AND bracket_id = $bracket_id" );
        return $match;
    }

    public static function get_match_by_bracket_match_and_playoff(int $bracket_match, int $bracket_id, int $playoff_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE bracket_match = $bracket_match AND bracket_id = $bracket_id AND playoff_id = $playoff_id" );
        return $match;
    }

    public static function get_matches_by_type(int $match_type, int $bracket_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE match_type = $match_type AND bracket_id = $bracket_id" );
        return $matches;
    }

    public static function get_match_by_match_link(int $bracket_id, int $match_link) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE $match_link IN (match_link_1, match_link_2) AND bracket_id = $bracket_id" );
        return $match;
    }

    public static function get_match_by_bracket_match_to_link(int $bracket_match, int $bracket_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_matches WHERE $bracket_match IN (match_link_1, match_link_2) AND bracket_id = $bracket_id" );
        return $match;
    }

    public static function insert_match(
        int $tournament_id, 
        int $division_id, 
        int $bracket_id, 
        int $field_number, 
        int $field_type,
        string $match_date, 
        int $match_time, 
        int $bracket_match, 
        int | null $official_id, 
        int | null $team_id_1, 
        int | null $team_id_2,
        int $match_type,
        int | null $playoff_id,
        int $bracket_round ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'tournament_id' => $tournament_id,
                'division_id' => $division_id,
                'official_id' => $official_id,
                'bracket_id' => $bracket_id,
                'field_number' => $field_number,
                'field_type' => $field_type,
                'goals_team_1' => null,
                'goals_team_2' => null,
                'team_id_1' => $team_id_1,
                'team_id_2' => $team_id_2,
                'match_date' => $match_date,
                'match_time' => $match_time,
                'bracket_match' => $bracket_match,
                'bracket_round' => $bracket_round,
                'match_link_1' => null,
                'match_link_2' => null,
                'match_type' => $match_type,
                'playoff_id' => $playoff_id,
                'match_pending' => true,
            )
        );

        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_match_link(int $match_id, int | null $match_link_1, int | null $match_link_2) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'match_link_1' => $match_link_1,
                'match_link_2' => $match_link_2,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match link updated successfully";
        }
        return "Match link not updated";
    }

    public static function update_match(int $match_id, int | null $team_id_1, int | null $team_id_2, int | null $goals_team_1, int | null $goals_team_2 ) {
       
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'team_id_1' => $team_id_1,
                'team_id_2' => $team_id_2,
                'goals_team_1' => $goals_team_1,
                'goals_team_2' => $goals_team_2,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match updated successfully";
        }
        return "Match not updated";
    }

    public static function update_match_team_1(int $match_id, int $team_id) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'team_id_1' => $team_id,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match team 1 updated successfully";
        }
        return "Match team 1 not updated";
    }

    public static function update_match_team_2(int $match_id, int $team_id) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'team_id_2' => $team_id,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match team 2 updated successfully";
        }
        return "Match team 2 not updated";
    }

    public static function update_match_official(int $match_id, int $official_id) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'official_id' => $official_id,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match official updated successfully";
        }
        return "Match official not updated";
    }

    public static function end_match(int $match_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'match_pending' => false,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match ended successfully";
        }
        return "Match not ended or match not found";
    }

    public static function delete_match(int $match_id ) {
        global $wpdb;
        $result = $wpdb->query(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match deleted successfully";
        }
        return "Match not deleted or match not found";
    }

    public static function delete_pending_matches_by_tournament(int $tournament_id ) {
        global $wpdb;
        $wpdb->show_errors();
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_pending_matches',
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return "Pending matches deleted successfully";
        }
        return "Pending matches not deleted or pending matches not found";
    }
}
