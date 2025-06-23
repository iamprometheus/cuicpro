<?php
declare(strict_types=1);

Class PendingPlayersDatabase {
    public static function init() {
        self::create_pending_players_table();
    }

    public static function create_pending_players_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_pending_players'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_pending_players (
            player_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            team_register_queue_id INT UNSIGNED NOT NULL,
            player_name VARCHAR(255) NOT NULL,
            player_photo VARCHAR(255) NOT NULL,
            player_visible BOOLEAN NOT NULL,
            PRIMARY KEY (player_id),
            FOREIGN KEY (team_register_queue_id) REFERENCES {$wpdb->prefix}cuicpro_team_register_queue(team_register_queue_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_players() {
        global $wpdb;
        $players = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_players WHERE player_visible = true" );
        return $players;
    }

    public static function get_player_by_id(int | null $player_id) {
        if (!$player_id) {
            return null;
        }
        global $wpdb;
        $player = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_players WHERE player_id = $player_id AND player_visible = true" );
        return $player;
    }

    public static function get_players_by_team_register_queue(int $team_register_queue_id) {
        global $wpdb;
        $players = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_pending_players WHERE team_register_queue_id = $team_register_queue_id AND player_visible = true" );
        return $players;
    }

    public static function insert_player(
      int $team_register_queue_id,
      string $player_name,
      string $player_photo ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_pending_players',
            array(
                'player_name' => $player_name,
                'team_register_queue_id' => $team_register_queue_id,
                'player_photo' => $player_photo,
                'player_visible' => true,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_player(
            int $player_id, 
            string $player_name, 
            string $player_photo ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_players',
            array(
                'player_name' => $player_name,
                'player_photo' => $player_photo,
                'player_visible' => true
            ),
            array(
                'player_id' => $player_id,
            )
        );
        if ( $result ) {
            return "Player updated successfully";
        }
        return "Player not updated";
    }

    public static function delete_player(int $player_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_players',
            array(
                'player_visible' => false,
            ),
            array(
                'player_id' => $player_id,
            )
        );
        if ( $result ) {
            return "Player deleted successfully";
        }
        return "Player not deleted or player not found";
    }

    public static function delete_players_by_team_register_queue(int $team_register_queue_id) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_pending_players',
            array(
                'player_visible' => false,
            ),
            array(
                'team_register_queue_id' => $team_register_queue_id,
            )
        );
        if ( $result ) {
            return "Players deleted successfully";
        }
        return "Players not deleted or players not found";
    }
}
