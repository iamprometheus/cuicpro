<?php

declare(strict_types=1);

class OfficialsUserDatabase
{
    public static function init()
    {
        self::create_officials_user_table();
    }

    public static function create_officials_user_table()
    {
        global $wpdb;
        //check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_officials_user'")) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_officials_user (
            user_id SMALLINT UNSIGNED NOT NULL,
            user_name VARCHAR(255) NOT NULL,
            user_contact VARCHAR(255) NOT NULL,
            user_city VARCHAR(255) NOT NULL,
            user_state VARCHAR(255) NOT NULL,
            user_country VARCHAR(255) NOT NULL,
            PRIMARY KEY (user_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function get_officials()
    {
        global $wpdb;
        $officials = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_officials_user");
        return $officials;
    }

    public static function get_official_by_id(int $user_id)
    {
        global $wpdb;
        $official = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_officials_user WHERE user_id = $user_id");
        return $official;
    }

    private static function official_exists(int $user_id)
    {
        global $wpdb;
        $official = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_officials_user WHERE user_id = $user_id");
        return $official;
    }

    public static function insert_official(int $user_id, string $official_name, string $official_contact, string $official_city, string $official_state, string $official_country)
    {
        global $wpdb;
        if (self::official_exists($user_id)) {
            return [false, null];
        }

        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_officials_user',
            array(
                'user_id' => $user_id,
                'user_name' => $official_name,
                'user_contact' => $official_contact,
                'user_city' => $official_city,
                'user_state' => $official_state,
                'user_country' => $official_country,
            )
        );

        if ($result) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_official(int $user_id, string $official_name, string $official_contact, string $official_city, string $official_state, string $official_country)
    {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_officials_user',
            array(
                'user_name' => $official_name,
                'user_contact' => $official_contact,
                'user_city' => $official_city,
                'user_state' => $official_state,
                'user_country' => $official_country,
            ),
            array(
                'user_id' => $user_id,
            )
        );
        if ($result) {
            return "Official updated successfully";
        }
        return "Official not updated";
    }

    public static function delete_official(int $user_id)
    {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_officials_user',
            array(
                'user_id' => $user_id,
            )
        );
        if ($result) {
            return "Official deleted successfully";
        }
        return "Official not deleted or official not found";
    }
}
