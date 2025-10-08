<?php

declare(strict_types=1);

class OfficialsRegisterQueueDatabase
{
    public static function init()
    {
        self::create_officials_register_queue_table();
    }

    public static function create_officials_register_queue_table()
    {
        global $wpdb;
        //check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_officials_register_queue'")) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_officials_register_queue (
            official_register_queue_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            official_user_id SMALLINT UNSIGNED NOT NULL,
            official_name VARCHAR(255) NOT NULL,
            official_contact VARCHAR(255) NULL,
            official_schedule VARCHAR(255) NULL,
            official_mode TINYINT UNSIGNED NOT NULL,
            official_city VARCHAR(255) NOT NULL,
            official_state VARCHAR(255) NOT NULL,
            official_country VARCHAR(255) NOT NULL,
            PRIMARY KEY (official_register_queue_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id),
            FOREIGN KEY (official_user_id) REFERENCES {$wpdb->prefix}cuicpro_officials_user(user_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function get_officials_register_queue()
    {
        global $wpdb;
        $officials_register_queue = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_officials_register_queue");
        return $officials_register_queue;
    }

    public static function get_officials_register_queue_by_id(int $officials_register_queue_id)
    {
        global $wpdb;
        $officials_register_queue = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_officials_register_queue WHERE official_register_queue_id = $officials_register_queue_id");
        return $officials_register_queue;
    }

    public static function get_officials_by_tournament(int $tournament_id)
    {
        global $wpdb;
        $officials = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_officials_register_queue WHERE tournament_id = $tournament_id");
        return $officials;
    }

    public static function get_official_registrations_by_official_id(int $official_user_id)
    {
        global $wpdb;
        $registrations = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_officials_register_queue WHERE official_user_id = $official_user_id");
        return $registrations;
    }

    public static function get_official_registration_by_tournament_and_official_id(int $tournament_id, int $official_user_id)
    {
        global $wpdb;
        $registration = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_officials_register_queue WHERE tournament_id = $tournament_id AND official_user_id = $official_user_id");
        return $registration;
    }

    public static function is_official_pending(int $official_user_id)
    {
        global $wpdb;
        $official = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_officials_register_queue WHERE official_user_id = $official_user_id");
        if ($official) {
            return true;
        }
        return false;
    }

    public static function insert_official(
        int $tournament_id,
        int $official_user_id,
        string $official_name,
        string $official_contact,
        string $official_schedule,
        int $official_mode,
        string $official_city,
        string $official_state,
        string $official_country
    ) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_officials_register_queue',
            array(
                'tournament_id' => $tournament_id,
                'official_user_id' => $official_user_id,
                'official_name' => $official_name,
                'official_contact' => $official_contact,
                'official_schedule' => $official_schedule,
                'official_mode' => $official_mode,
                'official_city' => $official_city,
                'official_state' => $official_state,
                'official_country' => $official_country,
            )
        );

        if ($result) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function delete_official(int $official_register_queue_id)
    {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_officials_register_queue',
            array(
                'official_register_queue_id' => $official_register_queue_id,
            )
        );
        if ($result) {
            return "Official deleted successfully";
        }
        return "Official not deleted or official not found";
    }
}
