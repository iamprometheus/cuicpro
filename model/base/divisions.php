<?php
declare(strict_types=1);

Class DivisionsDatabase {
    public static function init() {
        self::create_divisions_table();
    }
    
    public static function create_divisions_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_divisions'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_divisions (
            tournament_id SMALLINT UNSIGNED NOT NULL,
            division_name VARCHAR(255) NOT NULL,
            division_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            division_mode TINYINT UNSIGNED NOT NULL,
            division_min_teams TINYINT UNSIGNED NOT NULL,
            division_max_teams TINYINT UNSIGNED NOT NULL,
            division_category TINYINT UNSIGNED NOT NULL,
            division_is_active BOOLEAN NOT NULL,
            division_visible BOOLEAN NOT NULL,
            division_preferred_days VARCHAR(255) NOT NULL,
            PRIMARY KEY (division_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
            FOREIGN KEY (division_mode) REFERENCES {$wpdb->prefix}cuicpro_modes(mode_id),
            FOREIGN KEY (division_category) REFERENCES {$wpdb->prefix}cuicpro_categories(category_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    public static function get_divisions() {
        global $wpdb;
        $divisions = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_visible = true" );
        return $divisions;
    }

    public static function get_divisions_by_tournament(int $tournament_id) {
        global $wpdb;
        $divisions = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_visible = true AND tournament_id = $tournament_id" );
        return $divisions;
    }

    public static function get_active_divisions_by_tournament(int $tournament_id) {
        global $wpdb;
        $divisions = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_visible = true AND tournament_id = $tournament_id AND division_is_active = true" );
        return $divisions;
    }

    public static function get_division_by_id(int $division_id) {
        global $wpdb;
        $division = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_id = %d AND division_visible = true", $division_id) );
        return $division;
    }

    public static function get_division_by_name(string $division_name, int $tournament_id, int $division_mode, int $division_category) {
        global $wpdb;
        $division = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_name = %s AND tournament_id = %d AND division_mode = %d AND division_category = %d AND division_visible = true", $division_name, $tournament_id, $division_mode, $division_category) );
        return $division;
    }

    public static function insert_division( string $division_name, int $tournament_id, int $division_mode, int $division_min_teams, int $division_max_teams, int $division_category, string $division_preferred_days ) {
        if ( self::division_exists(null, $division_name, $division_mode, $division_category, $tournament_id ) ) {
            return [false, null];
        }

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_name' => $division_name,
                'tournament_id' => $tournament_id,
                'division_mode' => $division_mode,
                'division_min_teams' => $division_min_teams,
                'division_max_teams' => $division_max_teams,
                'division_category' => $division_category,
                'division_preferred_days' => $division_preferred_days,
                'division_is_active' => true,
                'division_visible' => true
            )
        );
        if ( $result ) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_division(int $division_id, string $division_name, int $division_mode, int $division_min_teams, int $division_max_teams, int $division_category, string $division_preferred_days, int $tournament_id, bool $visible ) {
        if ( self::division_exists($division_id, $division_name, $division_mode, $division_category, $tournament_id ) ) {
            return "Division with this name already exists";
        }
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_name' => $division_name,
                'division_mode' => $division_mode,
                'division_min_teams' => $division_min_teams,
                'division_max_teams' => $division_max_teams,
                'division_category' => $division_category,
                'division_preferred_days' => $division_preferred_days,
                'division_visible' => $visible,
            ),
            array(
                'division_id' => $division_id,
            )
        );
        if ( $result ) {
            return "Division updated successfully";
        }
        return "Division not updated or division not found";
    }

    public static function update_division_active(int $division_id, int $active ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_is_active' => $active,
            ),
            array(
                'division_id' => $division_id,
            )
        );
        if ( $result ) {
            return "Division active status updated successfully";
        }
        return "Division active status not updated";
    }

    public static function update_division_preferred_days(int $division_id, string $preferred_days ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_preferred_days' => $preferred_days,
            ),
            array(
                'division_id' => $division_id,
            )
        );
        if ( $result ) {
            return "Division preferred days updated successfully";
        }
        return "Division preferred days not updated";
    }

    public static function delete_division(int $division_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_visible' => false,
                'division_is_active' => false,
            ),
            array(
                'division_id' => $division_id,
            )
        );
        if ( $result ) {
            return "Division deleted successfully";
        }
        return "Division not deleted or division not found";
    }

    public static function division_exists(int | null $division_id, string $division_name, int $division_mode, int $division_category, int $tournament_id ) {
        global $wpdb;
        if (is_null($division_id)) {
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_name = %s AND division_mode = %d AND division_category = %d AND tournament_id = %d AND division_visible = true", $division_name, $division_mode, $division_category, $tournament_id);
        } else {
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_id != %d AND division_name = %s AND division_mode = %d AND division_category = %d AND tournament_id = %d AND division_visible = true", $division_id, $division_name, $division_mode, $division_category, $tournament_id);
        }
        $division = $wpdb->get_row( $sql );
        return $division;
    }
}
