<?php
declare(strict_types=1);

Class OfficialsHoursDatabase {
    public static function init() {
        self::create_officials_hours_table();
    }
    
    public static function create_officials_hours_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_officials_hours'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_officials_hours (
            official_hours_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            official_id SMALLINT UNSIGNED NOT NULL,
            official_day VARCHAR(255) NOT NULL,
            official_hours VARCHAR(255) NOT NULL,
            official_available_hours VARCHAR(255) NOT NULL,
            PRIMARY KEY (official_hours_id),
            FOREIGN KEY (official_id) REFERENCES {$wpdb->prefix}cuicpro_officials(official_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    public static function get_officials_hours() {
        global $wpdb;
        $officials_hours = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_officials_hours" );
        return $officials_hours;
    }

    public static function get_official_hours(int $official_id) {
        global $wpdb;
        $officials_hours = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_officials_hours WHERE official_id = %d", $official_id) );
        return $officials_hours;
    }

    public static function get_official_hours_by_day(int $official_id, string $official_day) {
      global $wpdb;
      $officials_hours = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_officials_hours WHERE official_id = $official_id AND official_day = '$official_day'");
      return $officials_hours;
  }

    public static function insert_official_hours(int $official_id, string $official_day, string $official_hours ) {
      global $wpdb;
      $result = $wpdb->insert(
        $wpdb->prefix . 'cuicpro_officials_hours',
        array(
          'official_id' => $official_id,
          'official_day' => $official_day,
          'official_hours' => $official_hours,
          'official_available_hours' => $official_hours,
        )
      );
      if ( $result ) {
        return "Official hours inserted successfully";
      }
      return "Official hours not inserted. Please try again.";
    }

    public static function update_officials_hours(int $official_hours_id, string $official_hours, string $official_available_hours ) {
      global $wpdb;
        $result = $wpdb->update(
          $wpdb->prefix . 'cuicpro_officials_hours',
          array(
            'official_hours' => $official_hours,
            'official_available_hours' => $official_available_hours,
          ),
          array(
            'official_hours_id' => $official_hours_id,
          )
        );
        if ( $result ) {
          return "Official hours updated successfully";
        }
        return "Official hours not updated. Please try again.";
    }

    public static function reset_official_available_hours(int $official_hours_id, string $official_hours ) {
      global $wpdb;
        $result = $wpdb->update(
          $wpdb->prefix . 'cuicpro_officials_hours',
          array(
            'official_available_hours' => $official_hours,
          ),
          array(
            'official_hours_id' => $official_hours_id,
          )
        );
        if ( $result ) {
          return "Official available hours reset successfully";
        }
        return "Official available hours not reset. Please try again.";
    }

    public static function update_official_available_hours(int $official_hours_id, string $new_official_available_hours ) {
      global $wpdb;
        $result = $wpdb->update(
          $wpdb->prefix . 'cuicpro_officials_hours',
          array(
            'official_available_hours' => $new_official_available_hours,
          ),
          array(
            'official_hours_id' => $official_hours_id,
          )
        );
        if ( $result ) {
          return "Official available hours updated successfully";
        }
        return "Official available hours not updated. Please try again.";
    }

    public static function official_id_exists(int $official_id ) {
      global $wpdb;
      $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_officials WHERE official_id = %d", $official_id);
      $official = $wpdb->get_row( $sql );
      return $official;
    }

    public static function delete_official_hours(int $official_id ) {
      global $wpdb;
      $wpdb->show_errors();
      $result = $wpdb->delete(
          $wpdb->prefix . 'cuicpro_officials_hours',
          array(
              'official_id' => $official_id,
          )
      );
      if ( $result ) {
          return "Official hours deleted successfully";
      }
      return "Official hours not deleted or official hours not found";
    }

    public static function delete_official_hours_by_id(int $official_hours_id ) {
      global $wpdb;
      $wpdb->show_errors();
      $result = $wpdb->delete(
          $wpdb->prefix . 'cuicpro_officials_hours',
          array(
              'official_hours_id' => $official_hours_id,
          )
      );
      if ( $result ) {
          return "Official hours deleted successfully";
      }
      return "Official hours not deleted or official hours not found";
    }
}
