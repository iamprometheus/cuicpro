<?php
declare(strict_types=1);

Class CoachesUserDatabase {
    public static function init() {
        self::create_coaches_user_table();
    }

    public static function create_coaches_user_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_coaches_user'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_coaches_user (
            user_id SMALLINT UNSIGNED NOT NULL,
            user_name VARCHAR(255) NOT NULL,
            user_contact VARCHAR(255) NOT NULL,
            user_city VARCHAR(255) NOT NULL,
            user_state VARCHAR(255) NOT NULL,
            user_country VARCHAR(255) NOT NULL,
            PRIMARY KEY (user_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_coaches() {
        global $wpdb;
        $coaches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches_user" );
        return $coaches;
    }

    public static function get_coach_by_id(int $user_id) {
        global $wpdb;
        $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches_user WHERE user_id = $user_id" );
        return $coach;
    }

    public static function get_coach_by_name( int | null $user_id, string $coach_name) {
        global $wpdb;
        if ($user_id) {
            $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches_user WHERE user_id != $user_id AND user_name = '$coach_name'" );
        } else {
            $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches_user WHERE user_name = '$coach_name'" );
        }
        return $coach;
    }

    private static function coach_exists(int $user_id) {
        global $wpdb;
        $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches_user WHERE user_id = $user_id" );
        return $coach;
    }

    public static function insert_coach( int $user_id, string $coach_name, string $coach_contact, string $coach_city, string $coach_state, string $coach_country ) {
        global $wpdb;
        if (self::coach_exists($user_id)) {
            return [false, null];
        }

        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_coaches_user',
            array(
                'user_id' => $user_id,
                'user_name' => $coach_name,
                'user_contact' => $coach_contact,
                'user_city' => $coach_city,
                'user_state' => $coach_state,
                'user_country' => $coach_country,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_coach(int $user_id, string $coach_name, string $coach_contact, string $coach_city, string $coach_state, string $coach_country ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_coaches_user',
            array(
                'user_name' => $coach_name,
                'user_contact' => $coach_contact,
                'user_city' => $coach_city,
                'user_state' => $coach_state,
                'user_country' => $coach_country,
            ),
            array(
                'user_id' => $user_id,
            )
        );
        if ( $result ) {
            return "Coach updated successfully";
        }
        return "Coach not updated";
    }

    public static function delete_coach(int $user_id ) {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_coaches_user',
            array(
                'user_id' => $user_id,
            )
        );
        if ( $result ) {
            return "Coach deleted successfully";
        }
        return "Coach not deleted or coach not found";
    }
}
