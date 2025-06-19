<?php
declare(strict_types=1);

Class MatchesDatabase {
    public static function init() {
        self::create_matches_table();
    }

    public static function create_matches_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_matches'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_matches (
            match_id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            division_id SMALLINT UNSIGNED NOT NULL,
            bracket_id SMALLINT UNSIGNED NOT NULL,
            bracket_round SMALLINT UNSIGNED NOT NULL,
            bracket_match SMALLINT UNSIGNED NOT NULL,
            team_id_1 SMALLINT UNSIGNED NOT NULL,
            team_id_2 SMALLINT UNSIGNED NOT NULL,
            field_number SMALLINT UNSIGNED NOT NULL,
            official_id SMALLINT UNSIGNED NULL,
            match_date VARCHAR(255) NOT NULL,
            match_time TINYINT UNSIGNED NOT NULL,
            goals_team_1 INT UNSIGNED NULL,
            goals_team_2 INT UNSIGNED NULL,
            match_winner SMALLINT UNSIGNED NULL,
            pending_match_id MEDIUMINT UNSIGNED NOT NULL,
            PRIMARY KEY (match_id),
            FOREIGN KEY (pending_match_id) REFERENCES {$wpdb->prefix}cuicpro_pending_matches(match_id),
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
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches" );
        return $matches;
    }

    public static function get_matches_by_tournament(int $tournament_id) {
      global $wpdb;
      $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE tournament_id = $tournament_id" );
      return $matches;
  }
    
    public static function get_matches_by_division(int $division_id, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE division_id = $division_id AND tournament_id = $tournament_id" );
        return $matches;
    }

    public static function get_matches_by_team(int $team_id, int $tournament_id) {
        global $wpdb;
        $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE team_id_1 = $team_id OR team_id_2 = $team_id AND tournament_id = $tournament_id" );
        return $matches;
    }

    public static function get_matches_by_bracket_team(int $team_id, int $bracket_id) {
      global $wpdb;
      $matches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE team_id_1 = $team_id OR team_id_2 = $team_id AND bracket_id = $bracket_id" );
      return $matches;
    }

    public static function get_match_by_pending_match_id(int $pending_match_id) {
      global $wpdb;
      $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE pending_match_id = $pending_match_id" );
      return $match;
    }

    public static function get_match_by_id(int $match_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE match_id = $match_id" );
        return $match;
    }

    public static function get_match_by_bracket_match(int $bracket_match, int $bracket_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE bracket_match = $bracket_match AND bracket_id = $bracket_id" );
        return $match;
    }

    public static function insert_match(
        int $tournament_id, 
        int $division_id,  
        int $bracket_id, 
        int $bracket_round, 
        int $bracket_match, 
        int $field_number, 
        int $team_id_1, 
        int $team_id_2, 
        int | null $official_id, 
        string $match_date, 
        int $match_time, 
        int | null $goals_team_1, 
        int | null $goals_team_2, 
        int | null $match_winner,
        int $pending_match_id
        ) {

        if (self::match_exists($pending_match_id)) {
            return false;
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_matches',
            array(
                'tournament_id' => $tournament_id,
                'division_id' => $division_id,
                'bracket_id' => $bracket_id,
                'bracket_round' => $bracket_round,
                'bracket_match' => $bracket_match,
                'field_number' => $field_number,
                'team_id_1' => $team_id_1,
                'team_id_2' => $team_id_2,
                'official_id' => $official_id,
                'match_date' => $match_date,
                'match_time' => $match_time,
                'goals_team_1' => $goals_team_1,
                'goals_team_2' => $goals_team_2,
                'match_winner' => $match_winner,
                'pending_match_id' => $pending_match_id,
            )
        );

        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_match(int $match_id, int $team_id_1, int $team_id_2, int $goals_team_1, int $goals_team_2, int | null $match_winner ) {
       
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_matches',
            array(
                'team_id_1' => $team_id_1,
                'team_id_2' => $team_id_2,
                'goals_team_1' => $goals_team_1,
                'goals_team_2' => $goals_team_2,
                'match_winner' => $match_winner,
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

    public static function update_match_winner(int $match_id, int | null $match_winner ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_matches',
            array(
                'match_winner' => $match_winner,
            ),
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match winner updated successfully";
        }
        return "Match winner not updated";
    }

    public static function get_goals_in_favor_by_team(int $team_id) {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE team_id_1 = $team_id OR team_id_2 = $team_id" );
        $goals = 0;
        foreach ($results as $result) {
            if ($result->team_id_1 == $team_id) {
                $goals += $result->goals_team_1;
            }
            if ($result->team_id_2 == $team_id) {
                $goals += $result->goals_team_2;
            }
        }
        return $goals;
    }

    public static function get_goals_against_by_team(int $team_id) {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE team_id_1 = $team_id OR team_id_2 = $team_id" );
        $goals = 0;
        foreach ($results as $result) {
            if ($result->team_id_1 == $team_id) {
                $goals += $result->goals_team_2;
            }
            if ($result->team_id_2 == $team_id) {
                $goals += $result->goals_team_1;
            }
        }
        return $goals;
    }

    public static function delete_match(int $match_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_matches',
            array(
                'match_id' => $match_id,
            )
        );
        if ( $result ) {
            return "Match deleted successfully";
        }
        return "Match not deleted or match not found";
    }

    public static function delete_matches_by_tournament(int $tournament_id ) {
      global $wpdb;
      $wpdb->show_errors();
      $result = $wpdb->delete(
          $wpdb->prefix . 'cuicpro_matches',
          array(
              'tournament_id' => $tournament_id,
          )
      );
      if ( $result ) {
          return "Matches deleted successfully";
      }
      return "Matches not deleted or matches not found";
    }

    public static function match_exists(int $pending_match_id) {
        global $wpdb;
        $match = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_matches WHERE pending_match_id = $pending_match_id" );
        return $match;
    }
}
