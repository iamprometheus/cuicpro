<?php
declare(strict_types=1);

Class LeaguesDatabase {
    public static function create_leagues_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_leagues'" ) ) {
            echo "<script>console.log('Leagues table already exists!');</script>";
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_leagues (
            league_id SMALLINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
            league_name VARCHAR(255) NOT NULL,
            PRIMARY KEY (league_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        echo "<script>console.log('Leagues table created successfully!');</script>";
    }
    
    public static function get_leagues() {
        global $wpdb;
        $leagues = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_leagues" );
        return $leagues;
    }

    public static function insert_league(string $league_name ) {
        if ( self::league_exists( $league_name ) ) {
            return "League with this name already exists";
        }

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_leagues',
            array(
                'league_name' => $league_name,
            )
        );
        if ( $result ) {
            return "League inserted successfully";
        }
        return "League not inserted or league already exists";
    }

    public static function update_league(int $league_id, string $league_name ) {
        if ( self::league_exists( $league_name ) ) {
            return "League with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_leagues',
            array(
                'league_name' => $league_name,
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
