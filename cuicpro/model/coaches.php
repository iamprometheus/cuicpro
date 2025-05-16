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
            coach_id SMALLINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
            coach_name VARCHAR(255) NOT NULL,
            coach_mode VARCHAR(255) NOT NULL,
            coach_phone VARCHAR(255) NOT NULL,
            PRIMARY KEY (coach_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_coaches() {
        global $wpdb;
        $coaches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches" );
        return $coaches;
    }

    public static function get_coach_by_id(int $coach_id) {
        global $wpdb;
        $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_id = $coach_id" );
        return $coach;
    }

    public static function get_coach_by_name(string $coach_name) {
        global $wpdb;
        $coach = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_coaches WHERE coach_name = '$coach_name'" );
        return $coach;
    }

    public static function insert_coach(string $coach_name, string $coach_mode, string $coach_phone ) {
        if ( self::get_coach_by_name( $coach_name ) ) {
            return false;
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_coaches',
            array(
                'coach_name' => $coach_name,
                'coach_mode' => $coach_mode,
                'coach_phone' => $coach_phone,
            )
        );

        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_coach(int $coach_id, string $coach_name, string $coach_mode, string $coach_phone ) {
        if ( self::get_coach_by_name( $coach_name ) ) {
            return "Coach with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_coaches',
            array(
                'coach_name' => $coach_name,
                'coach_mode' => $coach_mode,
                'coach_phone' => $coach_phone,
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
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_coaches',
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
