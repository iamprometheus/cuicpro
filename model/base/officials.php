<?php
declare(strict_types=1);

Class OfficialsDatabase {
    public static function init() {
        self::create_officials_table();
    }

    public static function create_officials_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_officials'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_officials (
            official_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            official_name VARCHAR(255) NOT NULL,
            official_hours TINYINT UNSIGNED NOT NULL,
            official_schedule VARCHAR(255) NOT NULL,
            official_mode TINYINT UNSIGNED NOT NULL,
            official_team_id SMALLINT UNSIGNED NULL,
            official_city VARCHAR(255) NOT NULL,
            official_state VARCHAR(255) NOT NULL,
            official_country VARCHAR(255) NOT NULL,
            official_is_active BOOLEAN NOT NULL,
            official_visible BOOLEAN NOT NULL,
            PRIMARY KEY (official_id),
            FOREIGN KEY (official_team_id) REFERENCES {$wpdb->prefix}cuicpro_teams(team_id),
            FOREIGN KEY (official_mode) REFERENCES {$wpdb->prefix}cuicpro_modes(mode_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_officials() {
        global $wpdb;
        $officials = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_visible = true" );
        return $officials;
    }

    public static function get_official_by_id(int $official_id) {
        global $wpdb;
        $official = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_id = $official_id AND official_visible = true" );
        return $official;
    }

    public static function get_official_by_name(string $official_name) {
        global $wpdb;
        $official = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_name = '$official_name' AND official_visible = true" );
        return $official;
    }

    public static function insert_official(
        string $official_name, 
        int $official_hours, 
        string $official_schedule, 
        int $official_mode, 
        int | null $official_team_id, 
        string $official_city, 
        string $official_state, 
        string $official_country ) {
        if ( self::get_official_by_name( $official_name ) ) {
            return false;
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'official_name' => $official_name,
                'official_hours' => $official_hours,
                'official_schedule' => $official_schedule,
                'official_mode' =>  $official_mode,
                'official_team_id' => $official_team_id,
                'official_city' => $official_city,
                'official_state' => $official_state,
                'official_country' => $official_country,
                'official_is_active' => true,
                'official_visible' => true,
            )
        );

        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_official(int $official_id, string $official_name, int $official_hours, string $official_schedule, int $official_mode, int $official_team_id, string $official_city, string $official_state, string $official_country, bool $official_is_active, bool $official_visible ) {
        if ( self::get_official_by_name( $official_name ) ) {
            return "Official with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'official_name' => $official_name,
                'official_hours' => $official_hours,
                'official_schedule' => $official_schedule,
                'official_mode' =>  $official_mode,
                'official_team_id' => $official_team_id,
                'official_city' => $official_city,
                'official_state' => $official_state,
                'official_country' => $official_country,
                'official_is_active' => $official_is_active,
                'official_visible' => $official_visible,
            ),
            array(
                'official_id' => $official_id,
            )
        );
        if ( $result ) {
            return "Official updated successfully";
        }
        return "Official not updated";
    }

    public static function delete_official(int $official_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'official_visible' => false,
                'official_is_active' => false,
            ),
            array(
                'official_id' => $official_id,
            )
        );
        if ( $result ) {
            return "Official deleted successfully";
        }
        return "Official not deleted or official not found";
    }
}
