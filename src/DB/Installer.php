<?php

declare(strict_types=1);

namespace OpinerMe\DB;

defined( 'ABSPATH' ) || exit;

class Installer {

    public static function activate(): void {
        self::create_opinions_table();
        self::create_ratings_table();
    }

    private static function create_opinions_table(): void {
        global $wpdb;

        $table_name      = $wpdb->prefix . 'om_opinions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            opinion_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            opinion_active TINYINT(1) NOT NULL DEFAULT 0,
            opinion_author VARCHAR(50) NOT NULL DEFAULT '',
            opinion_content TEXT NOT NULL,
            opinion_rating TINYINT(1) NOT NULL DEFAULT 0,
            opinion_ip VARCHAR(15) NOT NULL DEFAULT '',
            opinion_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (opinion_id),
            KEY post_id (post_id),
            KEY opinion_date (opinion_date),
            KEY opinion_ip_date (opinion_ip, opinion_date),
            KEY post_id_opinion_ip_date (post_id, opinion_ip, opinion_date)
        ) ENGINE=InnoDB $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( $sql );
    }

    private static function create_ratings_table(): void {
        global $wpdb;

        $table_name      = $wpdb->prefix . 'om_ratings';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            rating_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            rating_average FLOAT(3,2) NOT NULL DEFAULT 0.00,
            rating_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
            rating_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (rating_id),
            UNIQUE KEY post_id (post_id)
        ) ENGINE=InnoDB $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( $sql );
    }
}
