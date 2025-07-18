<?php
declare(strict_types=1);

Class PlayersUserDatabase {
    public static function init() {
        self::create_players_user_table();
    }

    public static function create_players_user_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_players_user'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_players_user (
            user_id SMALLINT UNSIGNED NOT NULL,
            user_name VARCHAR(255) NOT NULL,
            user_contact VARCHAR(255) NOT NULL,
            user_city VARCHAR(255) NOT NULL,
            user_state VARCHAR(255) NOT NULL,
            user_country VARCHAR(255) NOT NULL,
            user_has_team BOOLEAN NOT NULL DEFAULT FALSE,
            PRIMARY KEY (user_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_players() {
        global $wpdb;
        $players = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_players_user" );
        return $players;
    }

    public static function get_player_by_id(int $user_id) {
        global $wpdb;
        $player = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_players_user WHERE user_id = $user_id" );
        return $player;
    }

    private static function player_exists(int $user_id) {
        global $wpdb;
        $player = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_players_user WHERE user_id = $user_id" );
        return $player;
    }

    public static function insert_player( int $user_id, string $player_name, string $player_contact, string $player_city, string $player_state, string $player_country ) {
        global $wpdb;
        if (self::player_exists($user_id)) {
            return [false, null];
        }

        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_players_user',
            array(
                'user_id' => $user_id,
                'user_name' => $player_name,
                'user_contact' => $player_contact,
                'user_city' => $player_city,
                'user_state' => $player_state,
                'user_country' => $player_country,
                'user_has_team' => false,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_player(int $user_id, string $player_name, string $player_contact, string $player_city, string $player_state, string $player_country ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_players_user',
            array(
                'user_name' => $player_name,
                'user_contact' => $player_contact,
                'user_city' => $player_city,
                'user_state' => $player_state,
                'user_country' => $player_country
            ),
            array(
                'user_id' => $user_id,
            )
        );
        if ( $result ) {
            return "Player updated successfully";
        }
        return "Player not updated";
    }

    public static function update_player_has_team(int $user_id, bool $has_team ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_players_user',
            array(
                'user_has_team' => $has_team
            ),
            array(
                'user_id' => $user_id,
            )
        );
        if ( $result ) {
            return "Player has team updated successfully";
        }
        return "Player has team not updated";
    }

    public static function delete_player(int $user_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_players_user',
            array(
                'user_id' => $user_id,
            )
        );
        if ( $result ) {
            return "Player deleted successfully";
        }
        return "Player not deleted or player not found";
    }
}
