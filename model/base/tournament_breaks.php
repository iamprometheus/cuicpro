<?php

declare(strict_types=1);

class TournamentBreaksDatabase
{
    public static function init()
    {
        self::create_tournament_breaks_table();
    }

    public static function create_tournament_breaks_table()
    {
        global $wpdb;
        //check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_tournament_breaks'")) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_tournament_breaks (
            tournament_break_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id SMALLINT UNSIGNED NOT NULL,
            tournament_days VARCHAR(255) NOT NULL,
            tournament_break_hour TINYINT NOT NULL,
            tournament_break_reason VARCHAR(255) NOT NULL,
            PRIMARY KEY (tournament_break_id),
            FOREIGN KEY (tournament_id) REFERENCES {$wpdb->prefix}cuicpro_tournaments(tournament_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function get_tournament_breaks(int $tournament_id)
    {
        global $wpdb;
        $tournaments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_tournament_breaks WHERE tournament_id = {$tournament_id}");
        return $tournaments;
    }

    public static function get_tournament_breaks_by_id(int $tournament_break_id)
    {
        global $wpdb;
        $tournament = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournament_breaks WHERE tournament_break_id = %d", $tournament_break_id));
        return $tournament;
    }

    public static function get_tournament_breaks_by_tournament(int $tournament_id)
    {
        global $wpdb;
        $tournament = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournament_breaks WHERE tournament_id = %d", $tournament_id));
        return $tournament;
    }

    public static function insert_tournament_breaks(int $tournament_id, string $tournament_days, int $tournament_break_hour, string $tournament_break_reason)
    {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_tournament_breaks',
            array(
                'tournament_id' => $tournament_id,
                'tournament_days' => $tournament_days,
                'tournament_break_hour' => $tournament_break_hour,
                'tournament_break_reason' => $tournament_break_reason,
            )
        );
        if ($result) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function update_tournament_break(int $tournament_break_id, string $tournament_days, int $tournament_break_hour, string $tournament_break_reason)
    {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_tournament_breaks',
            array(
                'tournament_days' => $tournament_days,
                'tournament_break_hour' => $tournament_break_hour,
                'tournament_break_reason' => $tournament_break_reason,
            ),
            array(
                'tournament_break_id' => $tournament_break_id,
            )
        );
        if ($result) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function tournament_id_exists(int $tournament_id)
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_tournaments WHERE tournament_id = %d", $tournament_id);
        $tournament = $wpdb->get_row($sql);
        return $tournament;
    }

    public static function delete_tournament_breaks_by_tournament(int $tournament_id)
    {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_tournament_breaks',
            array(
                'tournament_id' => $tournament_id,
            )
        );
        if ($result) {
            return "Tournament breaks deleted successfully";
        }
        return "Tournament breaks not deleted or tournament breaks not found";
    }

    public static function delete_tournament_breaks_by_id(int $tournament_break_id)
    {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_tournament_breaks',
            array(
                'tournament_break_id' => $tournament_break_id,
            )
        );
        if ($result) {
            return "Tournament breaks deleted successfully";
        }
        return "Tournament breaks not deleted or tournament breaks not found";
    }
}
