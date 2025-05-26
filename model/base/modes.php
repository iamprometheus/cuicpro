<?php
declare(strict_types=1);

// Should always have this setup
// id 1 - 5v5
// id 2 - 7v7
// id 3 - Ambos

Class ModesDatabase {
    public static function init() {
        self::create_modes_table();
        self::insert_default_modes();
    }

    public static function create_modes_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_modes'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_modes (
            mode_id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
            mode_description VARCHAR(255) NOT NULL,
            PRIMARY KEY (mode_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_modes() {
        global $wpdb;
        $modes = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_modes" );
        return $modes;
    }

    public static function get_mode_by_id(int $mode_id) {
      global $wpdb;
      $modes = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_modes WHERE mode_id = '$mode_id'" );
      return $modes;
    }

    public static function get_mode_by_description(string $mode_description) {
      global $wpdb;
      $mode = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_modes WHERE mode_description = '$mode_description'" );
      return $mode;
    }

    public static function insert_mode(string $mode_description ) {
      if ( self::get_mode_by_description( $mode_description ) ) {
        return "This mode already exists";
      }

      global $wpdb;
      $result = $wpdb->insert(
        $wpdb->prefix . 'cuicpro_modes',
        array(
            'mode_description' => $mode_description,
          )
      );

      if ( $result ) {
          return true;
      }
      return false;
    }

    public static function insert_default_modes() {
      if ( !self::get_mode_by_description('5v5') ) {
        self::insert_mode('5v5');
      }
      if ( !self::get_mode_by_description('7v7') ) {
        self::insert_mode('7v7');
      }
      if ( !self::get_mode_by_description('Ambos') ) {
        self::insert_mode('Ambos');
      }
    }

    public static function update_mode( int $mode_id , string $mode_description ) {
      if ( self::get_mode_by_description( $mode_description ) && self::get_mode_by_id( $mode_id )->mode_description != $mode_description ) {
        return "This mode already exists";
      }

      global $wpdb;
      $result = $wpdb->update(
        $wpdb->prefix . 'cuicpro_modes',
        array(
            'mode_description' => $mode_description,
          ),
          array(
              'mode_id' => $mode_id,
          )
      );

      if ( $result ) {
          return true;
      }
      return false;
    }

    public static function delete_mode(int $mode_id ) {
      if ( !self::get_mode_by_id( $mode_id ) ) {
        return "This mode doesn't exist";
      }
      global $wpdb;
      $result = $wpdb->delete(
        $wpdb->prefix . 'cuicpro_modes',
        array(
            'mode_id' => $mode_id,
        )
      );
      if ( $result ) {
          return true;
      }
      return false;
    }
}
