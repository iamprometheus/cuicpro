<?php
declare(strict_types=1);

Class TeamsDatabase {

    public static function create_teams_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_teams'" ) ) {
            echo "<script>console.log('Teams table already exists!');</script>";
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_teams (
            team_id tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
            team_name varchar(255) NOT NULL,
            league_id tinyint(2) UNSIGNED NOT NULL,
            PRIMARY KEY (team_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        echo "<script>console.log('Teams table created successfully!');</script>";
    }
    
    public static function get_teams() {
        global $wpdb;
        $teams = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_teams" );
        return $teams;
    }

    public static function insert_team(string $team_name, int $league_id ) {
        if ( self::team_exists( $team_name, $league_id ) ) {
            return "Team with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_name' => $team_name,
                'league_id' => $league_id,
            )
        );

        if ( $result ) {
            return "Team inserted successfully";
        }
        return "Team not inserted";
    }

    public static function update_team(int $team_id, string $team_name, int $league_id ) {
        if ( self::team_exists( $team_name, $league_id ) ) {
            return "Team with this name already exists in this league";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_teams',
            array(
                'team_name' => $team_name,
                'league_id' => $league_id,
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

    public static function team_exists(string $team_name, int $league_id ) {
        global $wpdb;
        $team = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE team_name = '$team_name' AND league_id = $league_id" );
        return $team;
    }
}
