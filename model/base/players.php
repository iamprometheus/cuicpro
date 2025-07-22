<?php
declare(strict_types=1);

Class PlayersDatabase {
    public static function init() {
        self::create_players_table();
    }

    public static function create_players_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_players'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_players (
            player_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            player_user_id SMALLINT UNSIGNED NULL,
            coach_id SMALLINT UNSIGNED NOT NULL,
            team_id SMALLINT UNSIGNED NOT NULL,
            player_name VARCHAR(255) NOT NULL,
            player_photo VARCHAR(255) NOT NULL,
            player_visible BOOLEAN NOT NULL,
            PRIMARY KEY (player_id),
            FOREIGN KEY (player_user_id) REFERENCES {$wpdb->prefix}cuicpro_players_user(user_id),
            FOREIGN KEY (coach_id) REFERENCES {$wpdb->prefix}cuicpro_coaches_user(user_id),
            FOREIGN KEY (team_id) REFERENCES {$wpdb->prefix}cuicpro_teams_user(team_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_players() {
        global $wpdb;
        $players = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_players WHERE player_visible = true" );
        return $players;
    }

    public static function get_player_by_id(int | null $player_id) {
        if (!$player_id) {
            return null;
        }
        global $wpdb;
        $player = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_players WHERE player_id = $player_id AND player_visible = true" );
        return $player;
    }

    public static function get_player_by_user_id(int $user_id) {
        global $wpdb;
        $player = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_players WHERE player_user_id = $user_id AND player_visible = true" );
        return $player;
    }

    public static function get_players_by_team(int | null $team_id) {
        if (!$team_id) {
            return [];
        }
        global $wpdb;
        $players = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_players WHERE team_id = $team_id AND player_visible = true" );
        return $players;
    }

    public static function get_players_by_coach(int $coach_id) {
        global $wpdb;
        $players = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_players WHERE coach_id = $coach_id AND player_visible = true" );
        return $players;
    }

    public static function insert_player(
        int | null $user_id,
        string $player_name,
        int $team_id,
        string $player_photo,
        int $coach_id ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_players',
            array(
                'player_user_id' => $user_id,
                'player_name' => $player_name,
                'team_id' => $team_id,
                'player_photo' => $player_photo,
                'coach_id' => $coach_id,
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
            $wpdb->prefix . 'cuicpro_players',
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

    public static function update_player_name(int $player_id, string $player_name ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_players',
            array(
                'player_name' => $player_name,
                'player_visible' => true
            ),
            array(
                'player_id' => $player_id,
            )
        );
        if ( $result ) {
            return "Player name updated successfully";
        }
        return "Player name not updated";
    }

    public static function delete_player(int $player_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_players',
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
}
