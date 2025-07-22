<?php
declare(strict_types=1);

Class CoachesDatabase {
    public static function init() {
        self::create_coaches_table();
    }

    public static function create_coaches_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_coaches'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_coaches (
            coach_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            coach_user_id SMALLINT UNSIGNED NULL,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            coach_name VARCHAR(255) NOT NULL,
            coach_contact VARCHAR(255) NOT NULL,
            coach_city VARCHAR(255) NOT NULL,
            coach_state VARCHAR(255) NOT NULL,
            coach_country VARCHAR(255) NOT NULL,
            coach_visible BOOLEAN NOT NULL,
            PRIMARY KEY (coach_id),
            FOREIGN KEY (coach_user_id) REFERENCES {$wpdb->prefix}users(user_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_coaches() {
        global $wpdb;
        $coaches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_visible = true" );
        return $coaches;
    }

    public static function get_coach_by_id(int $coach_id) {
        global $wpdb;
        $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_id = $coach_id AND coach_visible = true" );
        return $coach;
    }

    public static function get_coach_by_name( int | null $coach_id, string $coach_name) {
        global $wpdb;
        if ($coach_id) {
            $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_id != $coach_id AND coach_name = '$coach_name' AND coach_visible = true" );
        } else {
            $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_name = '$coach_name' AND coach_visible = true" );
        }
        return $coach;
    }

    public static function get_coaches_by_coach_user(int $coach_user_id) {
        global $wpdb;
        $coaches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_user_id = $coach_user_id AND coach_visible = true" );
        return $coaches;
    }

    public static function get_coaches_by_tournament(int | null $tournament_id) {
        if (is_null($tournament_id)) {
            return [];
        }
        global $wpdb;
        $coaches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE tournament_id = $tournament_id AND coach_visible = true" );
        return $coaches;
    }

    public static function get_coach_by_coach_user_and_tournament(int $coach_user_id, int $tournament_id) {
        global $wpdb;
        $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_user_id = $coach_user_id AND tournament_id = $tournament_id AND coach_visible = true" );
        return $coach;
    }

    public static function get_coaches_by_tournament_and_name(int $tournament_id, string $coach_name) {
        global $wpdb;
        $coaches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE tournament_id = $tournament_id AND coach_name = '$coach_name' AND coach_visible = true" );
        return $coaches;
    }

    public static function insert_coach(int | null $user_id, int $tournament_id, string $coach_name, string $coach_contact, string $coach_city, string $coach_state, string $coach_country ) {
        if ( self::get_coaches_by_tournament_and_name($tournament_id, $coach_name ) ) {
            return [false, null];
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_coaches',
            array(
                'coach_user_id' => $user_id,
                'tournament_id' => $tournament_id,
                'coach_name' => $coach_name,
                'coach_contact' => $coach_contact,
                'coach_city' => $coach_city,
                'coach_state' => $coach_state,
                'coach_country' => $coach_country,
                'coach_visible' => true,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_coach(int $coach_id, string $coach_name, string $coach_contact, string $coach_city, string $coach_state, string $coach_country, bool $coach_visible ) {
        if ( self::get_coach_by_name($coach_id, $coach_name ) ) {
            return "Coach with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_coaches',
            array(
                'coach_name' => $coach_name,
                'coach_contact' => $coach_contact,
                'coach_city' => $coach_city,
                'coach_state' => $coach_state,
                'coach_country' => $coach_country,
                'coach_visible' => $coach_visible,
            ),
            array(
                'coach_id' => $coach_id,
            )
        );
        if ( $result ) {
            return "Coach updated successfully";
        }
        return "Coach not updated";
    }

    public static function delete_coach(int $coach_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_coaches',
            array(
                'coach_visible' => false,
            ),
            array(
                'coach_id' => $coach_id,
            )
        );
        if ( $result ) {
            return "Coach deleted successfully";
        }
        return "Coach not deleted or coach not found";
    }
}
