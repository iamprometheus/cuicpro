<?php
declare(strict_types=1);

Class LeaguesDatabase {
    public static function init() {
        self::create_leagues_table();
    }
    
    public static function create_leagues_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_leagues'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_leagues (
            league_id SMALLINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
            league_name VARCHAR(255) NOT NULL,
            league_mode VARCHAR(255) NOT NULL,
            PRIMARY KEY (league_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    public static function get_leagues() {
        global $wpdb;
        $leagues = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_leagues" );
        return $leagues;
    }

    public static function get_league_by_id(int $league_id) {
        global $wpdb;
        $league = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_leagues WHERE league_id = %d", $league_id) );
        return $league;
    }

    public static function get_league_by_name(string $league_name) {
        global $wpdb;
        $league = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_leagues WHERE league_name = %s", $league_name) );
        return $league;
    }

    public static function insert_league(string $league_name, string $league_mode ) {
        if ( self::league_exists( $league_name ) ) {
            return false;
        }

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_leagues',
            array(
                'league_name' => $league_name,
                'league_mode' => $league_mode,
            )
        );
        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_league(int $league_id, string $league_name, string $league_mode ) {
        if ( self::league_exists( $league_name ) ) {
            return "League with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_leagues',
            array(
                'league_name' => $league_name,
                'league_mode' => $league_mode,
                ),
            array(
                'league_id' => $league_id,
            )
        );
        if ( $result ) {
            return "League updated successfully";
        }
        return "League not updated or league not found";
    }

    public static function delete_league(int $league_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_leagues',
            array(
                'league_id' => $league_id,
            )
        );
        if ( $result ) {
            return "League deleted successfully";
        }
        return "League not deleted or league not found";
    }

    public static function league_exists(string $league_name ) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_leagues WHERE league_name = %s", $league_name);
        $league = $wpdb->get_row( $sql );
        return $league;
    }
}
