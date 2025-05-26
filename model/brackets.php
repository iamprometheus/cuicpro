<?php
declare(strict_types=1);

Class BracketsDatabase {
    public static function init() {
        self::create_brackets_table();
    }

    public static function create_brackets_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_brackets'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_brackets (
            bracket_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            division_id SMALLINT UNSIGNED NULL,
            PRIMARY KEY (bracket_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
            FOREIGN KEY (division_id) REFERENCES {$wpdb->prefix}cuicpro_divisions(division_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_brackets() {
        global $wpdb;
        $brackets = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_brackets" );
        return $brackets;
    }

    public static function get_brackets_by_tournament(int $tournament_id) {
      global $wpdb;
      $brackets = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_brackets WHERE tournament_id = $tournament_id" );
      return $brackets;
  }
    
    public static function get_bracket_by_division(int $division_id, int $tournament_id) {
        global $wpdb;
        $brackets = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_brackets WHERE division_id = $division_id AND tournament_id = $tournament_id" );
        return $brackets;
    }

    public static function get_bracket_by_id(int $bracket_id) {
        global $wpdb;
        $bracket = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_brackets WHERE bracket_id = $bracket_id" );
        return $bracket;
    }

    public static function insert_bracket(int $tournament_id, int $division_id ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_brackets',
            array(
                'tournament_id' => $tournament_id,
                'division_id' => $division_id,
            )
        );

        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_bracket(int $bracket_id, int $tournament_id, int $division_id ) {
       
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_brackets',
            array(
                'tournament_id' => $tournament_id,
                'division_id' => $division_id,
            ),
            array(
                'bracket_id' => $bracket_id,
            )
        );
        if ( $result ) {
            return "Bracket updated successfully";
        }
        return "Bracket not updated or bracket not found";
    }

    public static function delete_bracket(int $bracket_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_brackets',
            array(
                'bracket_id' => $bracket_id,
            )
        );
        if ( $result ) {
            return "Bracket deleted successfully";
        }
        return "Bracket not deleted or bracket not found";
    }
}
