<?php
declare(strict_types=1);

Class TournamentsDatabase {
    public static function init() {
        self::create_tournaments_table();
    }
    
    public static function create_tournaments_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_tournaments'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_tournaments (
            tournament_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_name VARCHAR(255) NOT NULL,
            tournament_creation_date DATE NOT NULL,
            tournament_start_date DATE NULL,
            tournament_end_date DATE NULL,
            tournament_days VARCHAR(255) NOT NULL,
            tournament_type TINYINT NULL,
            tournament_fields_5v5 TINYINT NOT NULL,
            tournament_fields_7v7 TINYINT NOT NULL,
            tournament_is_active BOOLEAN NOT NULL,
            tournament_visible BOOLEAN NOT NULL,
            tournament_has_officials BOOLEAN NOT NULL,
            PRIMARY KEY (tournament_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    public static function get_tournaments() {
        global $wpdb;
        $tournaments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_tournaments where tournament_visible = 1" );
        return $tournaments;
    }

    public static function get_active_tournaments() {
        global $wpdb;
        $tournaments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_tournaments where tournament_is_active = 1 and tournament_visible = 1" );
        return $tournaments;
    }

    public static function get_active_tournaments_not_started() {
        global $wpdb;
        $tournaments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_tournaments where tournament_is_active = 1 and tournament_visible = 1 and tournament_start_date is null" );
        return $tournaments;
    }

    public static function get_tournament_by_id(int $tournament_id) {
        global $wpdb;
        $tournament = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_id = %d", $tournament_id) );
        return $tournament;
    }

    public static function get_tournament_by_name(string $tournament_name) {
        global $wpdb;
        $tournament = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_name = %s", $tournament_name) );
        return $tournament;
    }

    public static function get_active_tournament() {
        global $wpdb;
        $tournament = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_is_active = 1" );
        return $tournament;
    }

    public static function start_tournament(int $tournament_id, int $tournament_type ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_start_date' => date('Y-m-d'),
                'tournament_type' => $tournament_type,
            ),
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return "Tournament started successfully";
        }
        return "Tournament not started or tournament not found";
    }

    public static function end_tournament(int $tournament_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_is_active' => false,
                'tournament_end_date' => date('Y-m-d'),
            ),
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return "Tournament ended successfully";
        }
        return "Tournament not ended or tournament not found";
    }

    public static function reset_tournament(int $tournament_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_start_date' => null,
                'tournament_end_date' => null,
                'tournament_type' => null,
            ),
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return "Tournament reset successfully";
        }
        return "Tournament not reset or tournament not found";
    }

    public static function insert_tournament(string $tournament_name, string $tournament_days, int $tournament_fields_5v5, int $tournament_fields_7v7, string $tournament_creation_date ) {
        if ( self::tournament_exists( $tournament_name, null ) ) {
            return [
                "success" => false,
                "tournament_id" => null
            ];
        }

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_name' => $tournament_name,  
                'tournament_days' => $tournament_days,
                'tournament_fields_5v5' => $tournament_fields_5v5,
                'tournament_fields_7v7' => $tournament_fields_7v7,
                'tournament_creation_date' => $tournament_creation_date,
                'tournament_is_active' => true,
                'tournament_visible' => true,
                'tournament_has_officials' => false,
            )
        );
        if ( $result  ) {
            return [
                "success" => true,
                "tournament_id" => $wpdb->insert_id
            ];
        }
        return [
            "success" => false,
            "tournament_id" => null
        ];
    }

    public static function update_tournament(int $tournament_id, string $tournament_name, string $tournament_days, int $tournament_fields_5v5, int $tournament_fields_7v7 ) {
        if ( self::tournament_exists( $tournament_name, $tournament_id ) ) {
            return [
                "success" => false,
                "tournament_id" => $tournament_id
            ];
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_name' => $tournament_name,
                'tournament_days' => $tournament_days,
                'tournament_fields_5v5' => $tournament_fields_5v5,
                'tournament_fields_7v7' => $tournament_fields_7v7,
            ),
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return [
                "success" => true,
                "tournament_id" => $tournament_id
            ];
        }
        return [
            "success" => true,
            "tournament_id" => null
        ];
    }

    public static function update_tournament_has_officials(int $tournament_id, bool $tournament_has_officials ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_has_officials' => $tournament_has_officials,
            ),
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function delete_tournament(int $tournament_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournaments',
            array(
                'tournament_visible' => false,
                'tournament_is_active' => false,
                'tournament_start_date' => null,
                'tournament_end_date' => null,
            ),
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ( $result ) {
            return "Tournament deleted successfully";
        }
        return "Tournament not deleted or tournament not found";
    }

    public static function tournament_exists(string $tournament_name, int | null $tournament_id ) {
        global $wpdb;
        $sql = "";
        if ( $tournament_id ) {
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_name = %s and tournament_id != %d", $tournament_name, $tournament_id);
        } else {
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_name = %s", $tournament_name);
        }
        $tournament = $wpdb->get_row( $sql );
        return $tournament;
    }
}
