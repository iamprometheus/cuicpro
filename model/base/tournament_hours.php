<?php
declare(strict_types=1);

Class TournamentHoursDatabase {
    public static function init() {
        self::create_tournament_hours_table();
    }
    
    public static function create_tournament_hours_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_tournament_hours'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_tournament_hours (
            tournament_hours_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            tournament_day VARCHAR(255) NOT NULL,
            tournament_hours_start TINYINT NOT NULL,
            tournament_hours_end TINYINT NOT NULL,
            PRIMARY KEY (tournament_hours_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    public static function get_tournament_hours(int $tournament_id) {
        global $wpdb;
        $tournaments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_tournament_hours WHERE tournament_id = {$tournament_id}" );
        return $tournaments;
    }

    public static function get_tournament_hours_by_id(int $tournament_hours_id) {
        global $wpdb;
        $tournament = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournament_hours WHERE tournament_hours_id = %d", $tournament_hours_id) );
        return $tournament;
    }

    public static function get_tournament_hours_by_tournament(int $tournament_id) {
        global $wpdb;
        $tournament = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournament_hours WHERE tournament_id = %d", $tournament_id) );
        return $tournament;
    }

    public static function insert_tournament_hours(int $tournament_id, string $tournament_day, int $tournament_hours_start, int $tournament_hours_end ) {
      global $wpdb;
      $result = $wpdb->insert(
        $wpdb->prefix . 'cuicpro_tournament_hours',
        array(
          'tournament_id' => $tournament_id,
          'tournament_day' => $tournament_day,
          'tournament_hours_start' => $tournament_hours_start,
          'tournament_hours_end' => $tournament_hours_end,
        )
      );
      if ( $result ) {
        return "Tournament hours inserted successfully";
      }
      return "Tournament hours not inserted. Please try again.";
    }

    public static function update_tournament_hours(int $tournament_hours_id, int $tournament_id, string $tournament_day, int $tournament_hours_start, int $tournament_hours_end ) {
      if ( self::tournament_id_exists( $tournament_id ) ) {
        return "Tournament not found";
      }
      global $wpdb;
        $result = $wpdb->update(
          $wpdb->prefix . 'cuicpro_tournament_hours',
          array(
            'tournament_id' => $tournament_id,
            'tournament_day' => $tournament_day,
            'tournament_hours_start' => $tournament_hours_start,
            'tournament_hours_end' => $tournament_hours_end,
          ),
          array(
            'tournament_hours_id' => $tournament_hours_id,
          )
        );
        if ( $result ) {
          return "Tournament hours updated successfully";
        }
        return "Tournament hours not updated. Please try again.";
    }

    public static function tournament_id_exists(int $tournament_id ) {
      global $wpdb;
      $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_id = %d", $tournament_id);
      $tournament = $wpdb->get_row( $sql );
      return $tournament;
    }

    public static function delete_tournament_hours_by_tournament(int $tournament_id ) {
      global $wpdb;
      $wpdb->show_errors();
      $result = $wpdb->delete(
          $wpdb->prefix . 'cuicpro_tournament_hours',
          array(
              'tournament_id' => $tournament_id,
          )
      );
      if ( $result ) {
          return "Tournament hours deleted successfully";
      }
      return "Tournament hours not deleted or tournament hours not found";
    }
}
