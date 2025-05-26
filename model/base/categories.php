<?php
declare(strict_types=1);

// Should always have this setup
// id 1 - Varonil
// id 2 - Femenil
// id 3 - Mixto

Class CategoriesDatabase {
    public static function init() {
        self::create_categories_table();
        self::insert_default_categories();
    }

    public static function create_categories_table() {
        global $wpdb;
        //check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_categories'" ) ) {
            return;
        }
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_categories (
            category_id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
            category_description VARCHAR(255) NOT NULL,
            PRIMARY KEY (category_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_categories() {
        global $wpdb;
        $categories = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cuicpro_categories" );
        return $categories;
    }

    public static function get_category_by_id(int $category_id) {
      global $wpdb;
      $category = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_categories WHERE category_id = '$category_id'" );
      return $category;
    }

    public static function get_category_by_description(string $category_description) {
      global $wpdb;
      $category = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cuicpro_categories WHERE category_description = '$category_description'" );
      return $category;
  }

    public static function insert_category(string $category_description ) {
      if ( self::get_category_by_description( $category_description ) ) {
        return "This category already exists";
      }

      global $wpdb;
      $result = $wpdb->insert(
          $wpdb->prefix . 'cuicpro_categories',
          array(
              'category_description' => $category_description,
            )
        );

      if ( $result ) {
          return true;
      }
      return false;
    }

    public static function insert_default_categories() {
      if ( !self::get_category_by_description('Varonil') ) {
        self::insert_category('Varonil');
      }
      if ( !self::get_category_by_description('Femenil') ) {
        self::insert_category('Femenil');
      }
      if ( !self::get_category_by_description('Mixto') ) {
        self::insert_category('Mixto');
      }
    }

    public static function update_category( int $category_id , string $category_description ) {
      if ( self::get_category_by_description( $category_description ) && self::get_category_by_id( $category_id )->category_description != $category_description ) {
        return "This category already exists";
      }

      global $wpdb;
      $result = $wpdb->update(
          $wpdb->prefix . 'cuicpro_categories',
          array(
              'category_description' => $category_description,
          ),
          array(
              'category_id' => $category_id,
          )
      );

      if ( $result ) {
          return true;
      }
      return false;
    }

    public static function delete_category(int $category_id ) {
      if ( !self::get_category_by_id( $category_id ) ) {
        return "This category doesn't exist";
      }

      global $wpdb;
      $result = $wpdb->delete(
          $wpdb->prefix . 'cuicpro_categories',
          array(
              'category_id' => $category_id,
          )
      );
      if ( $result ) {
          return true;
      }
      return false;
    }
}
