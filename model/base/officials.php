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
            tournament_id SMALLINT UNSIGNED NOT NULL,
            official_user_id SMALLINT UNSIGNED NULL,
            official_name VARCHAR(255) NOT NULL,
            official_schedule VARCHAR(255) NULL,
            official_mode TINYINT UNSIGNED NOT NULL,
            official_team_id SMALLINT UNSIGNED NULL,
            official_city VARCHAR(255) NOT NULL,
            official_state VARCHAR(255) NOT NULL,
            official_country VARCHAR(255) NOT NULL,
            official_is_active BOOLEAN NOT NULL,
            official_visible BOOLEAN NOT NULL,
            official_is_certified BOOLEAN NOT NULL,
            PRIMARY KEY (official_id),
            FOREIGN KEY (official_user_id) REFERENCES {$wpdb->prefix}cuicpro_officials_user(user_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
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

    public static function get_official_by_id(int | null $official_id) {
        if (!$official_id) {
            return null;
        }
        global $wpdb;
        $official = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_id = $official_id AND official_visible = true" );
        return $official;
    }

    public static function get_official_by_name(string $official_name, int | null $official_id) {
        global $wpdb;
        if ($official_id) {
            $official = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_name = '$official_name' AND official_id != $official_id AND official_visible = true" );
        } else {
            $official = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_name = '$official_name' AND official_visible = true" );
        }
        return $official;
    }

    public static function get_officials_by_tournament(int $tournament_id) {
        global $wpdb;
        $officials = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE tournament_id = $tournament_id AND official_visible = true AND official_is_active = true" );
        return $officials;
    }

    public static function insert_official(
        int $tournament_id,
        int | null $official_user_id,
        string $official_name, 
        string | null $official_schedule, 
        int $official_mode, 
        int | null $official_team_id, 
        string $official_city, 
        string $official_state, 
        string $official_country ) {
        if ( self::get_official_by_name( $official_name, null ) ) {
            return [false, null];
        }
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'tournament_id' => $tournament_id,
                'official_user_id' => $official_user_id,
                'official_name' => $official_name,
                'official_schedule' => $official_schedule,
                'official_mode' =>  $official_mode,
                'official_team_id' => $official_team_id,
                'official_city' => $official_city,
                'official_state' => $official_state,
                'official_country' => $official_country,
                'official_is_active' => true,
                'official_visible' => true,
                'official_is_certified' => false,
            )
        );

        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_official(
            int $official_id, 
            string $official_name, 
            string | null $official_schedule, 
            int $official_mode, 
            int | null $official_team_id, 
            string $official_city, 
            string $official_state, 
            string $official_country, 
            bool $official_visible ) {
        if ( self::get_official_by_name( $official_name, $official_id ) ) {
            return "Official with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'official_name' => $official_name,
                'official_schedule' => $official_schedule,
                'official_mode' =>  $official_mode,
                'official_team_id' => $official_team_id,
                'official_city' => $official_city,
                'official_state' => $official_state,
                'official_country' => $official_country,
                'official_visible' => $official_visible
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

    public static function update_official_active(int $official_id, int $official_is_active) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'official_is_active' => $official_is_active,
            ),
            array(
                'official_id' => $official_id,
            )
        );
        if ( $result ) {
            return "Official active status updated successfully";
        }
        return "Official active status not updated";
    }

    public static function update_official_certified(int $official_id, int $official_is_certified) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_officials',
            array(
                'official_is_certified' => $official_is_certified,
            ),
            array(
                'official_id' => $official_id,
            )
        );
        if ( $result ) {
            return "Official certified status updated successfully";
        }
        return "Official certified status not updated";
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
