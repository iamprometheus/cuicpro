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
            division_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            division_name VARCHAR(255) NOT NULL,
            division_mode TINYINT UNSIGNED NOT NULL,
            division_min_teams TINYINT UNSIGNED NOT NULL,
            division_max_teams TINYINT UNSIGNED NOT NULL,
            division_category TINYINT UNSIGNED NOT NULL,
            division_visible BOOLEAN NOT NULL,
            PRIMARY KEY (division_id),
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

    public static function get_division_by_id(int $division_id) {
        global $wpdb;
        $division = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_id = %d AND division_visible = true", $division_id) );
        return $division;
    }

    public static function get_division_by_name(string $division_name, int $division_mode, int $division_category) {
        global $wpdb;
        $division = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_name = %s AND division_mode = %d AND division_category = %d AND division_visible = true", $division_name, $division_mode, $division_category) );
        return $division;
    }

    public static function insert_division(string $division_name, int $division_mode, int $division_min_teams, int $division_max_teams, int $division_category ) {
        if ( self::division_exists( $division_name, $division_mode, $division_category ) ) {
            return false;
        }

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_name' => $division_name,
                'division_mode' => $division_mode,
                'division_min_teams' => $division_min_teams,
                'division_max_teams' => $division_max_teams,
                'division_category' => $division_category,
                'division_visible' => true,
            )
        );
        if ( $result ) {
            return true;
        }
        return false;
    }

    public static function update_division(int $division_id, string $division_name, int $division_mode, int $division_min_teams, int $division_max_teams, int $division_category, bool $visible ) {
        if ( self::division_exists( $division_name, $division_mode, $division_category ) ) {
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

    public static function delete_division(int $division_id ) {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_divisions',
            array(
                'division_visible' => false,
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

    public static function division_exists(string $division_name, int $division_mode, int $division_category ) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_divisions WHERE division_name = %s AND division_mode = %d AND division_category = %d AND division_visible = true", $division_name, $division_mode, $division_category);
        $division = $wpdb->get_row( $sql );
        return $division;
    }
}
