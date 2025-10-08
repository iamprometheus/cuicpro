<?php

declare(strict_types=1);

class NotificationsDatabase
{
    public static function init()
    {
        self::create_notifications_table();
        self::create_notification_system_templates_entries();
    }

    public static function create_notifications_table()
    {
        global $wpdb;
        //check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_notifications'")) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cuicpro_notifications (
            notification_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
            notification_title VARCHAR(255) NOT NULL,
            notification_message VARCHAR(1000) NOT NULL,
            notification_type VARCHAR(255) NOT NULL,
            PRIMARY KEY (notification_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function create_notification_system_templates_entries()
    {
        global $wpdb;
        //check if table exists
        if (!$wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cuicpro_notifications'")) {
            return;
        }

        $entries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_notifications WHERE notification_type = 'system'");

        // if entries are empty, create them
        if (empty($entries)) {
            self::insert_notification(
                'Partidos hoy',
                '🏟️ ¡Nuevos partidos disponibles hoy! \n

                Hola [name], \n

                ¡Tenemos buenas noticias! Tienes partidos disponibles para el día de hoy. Es tu oportunidad para jugar, mejorar y conectar con otros jugadores y entrenadores de la comunidad. \n\n

                🏆 Torneo: [tournament] \n
                📅 Fecha: [date] \n
                ⏰ Hora: [time] \n
                📍 Campo: [field] \n
                ✅ Arbitro: [official] \n

                📲 Ingresa ahora a tu cuenta para ver los detalles: \n
                👉 cuic.pro/mi-perfil \n

                🏉 ¡Te esperamos! \n

                — El equipo de CÜIC',
                'system'
            );

            self::insert_notification(
                'Partidos mañana',
                '🏟️ ¡Nuevos partidos disponibles mañana! \n

                Hola [name], \n

                ¡Tenemos buenas noticias! Tienes partidos disponibles para mañana. Es tu oportunidad para jugar, mejorar y conectar con otros jugadores y entrenadores de la comunidad. \n

                🏆 Torneo: [tournament] \n
                📅 Fecha: [date] \n
                ⏰ Hora: [time] \n
                📍 Campo: [field] \n
                ✅ Arbitro: [official] \n

                📲 Ingresa ahora a tu cuenta para ver los detalles: \n
                👉 cuic.pro/mi-perfil \n

                🏉 ¡Te esperamos! \n

                — El equipo de CÜIC',
                'system'
            );

            self::insert_notification(
                'Horarios de partidos disponibles',
                '🏟️ ¡Horarios de partidos disponibles! \n

                Hola [name], \n

                ¡Tenemos buenas noticias! Ya estan disponibles los horarios de partidos.

                📲 Ingresa ahora a tu cuenta para ver los detalles: \n
                👉 cuic.pro/mi-perfil \n

                — El equipo de CÜIC',
                'system'
            );
        }
    }

    public static function insert_notification(string $notification_title, string $notification_message, string $notification_type)
    {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'cuicpro_notifications',
            array(
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_type' => $notification_type
            )
        );

        if ($result) {
            return [true, $wpdb->insert_id];
        }
        return [false, null];
    }

    public static function get_notifications()
    {
        global $wpdb;
        $entries = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_notifications");
        return $entries;
    }

    public static function get_notification(int $notification_id)
    {
        global $wpdb;
        $entry = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cuicpro_notifications WHERE notification_id = $notification_id");
        return $entry;
    }

    public static function get_system_notifications()
    {
        global $wpdb;
        $entry = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_notifications WHERE notification_type = 'system'");
        return $entry;
    }

    public static function update_notification(int $notification_id, string $notification_title, string $notification_message)
    {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_notifications',
            array(
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
            ),
            array(
                'notification_id' => $notification_id,
            )
        );
        if ($result) {
            return "Notification updated successfully";
        }
        return "Notification not updated";
    }

    public static function delete_notification(int $notification_id)
    {
        global $wpdb;
        $result = $wpdb->delete(
            $wpdb->prefix . 'cuicpro_notifications',
            array(
                'notification_id' => $notification_id,
            )
        );
        if ($result) {
            return "Notification deleted successfully";
        }
        return "Notification not deleted or notification not found";
    }

    public static function send_notification(int $notification_id)
    {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'cuicpro_notifications',
            array(
                'notification_active' => true,
            ),
            array(
                'notification_id' => $notification_id,
            )
        );
        if ($result) {
            return "Notification sent successfully";
        }
        return "Notification not sent or notification not found";
    }
}
