<?php

declare(strict_types=1);

namespace OpinerMe\DB;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;
use OpinerMe\Diagnostics\Logger;

class SchemaManager {

    public static function maybe_update_schema(): void {
        $installed_version = get_option( Config::OPTION_DB_VERSION, '0.0.0' );

        if ( $installed_version !== Config::VERSION ) {
            self::update_schema( $installed_version );

            update_option( Config::OPTION_DB_VERSION, Config::VERSION );
        }
    }

    private static function update_schema( ?string $from_version ): void {
        if ( ! isset( $from_version ) || version_compare( $from_version, '0.1.0', '<' ) ) {
            return;
        }

        global $wpdb;

        $table      = $wpdb->prefix . 'om_opinions';
        $table_name = esc_sql( $table );

        if ( version_compare( $from_version, '0.1.0', '<' ) ) {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name safely escaped and safe SHOW query on custom table, no caching needed
            $results = $wpdb->get_results( "SHOW COLUMNS FROM `{$table_name}` LIKE 'opinion_source'" );

            if ( empty( $results ) ) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- table name safely escaped, safe ALTER query on custom table, no caching needed, and safe schema change on custom table during plugin upgrade
                $wpdb->query( "ALTER TABLE `{$table_name}` ADD COLUMN opinion_source VARCHAR(50) NOT NULL DEFAULT ''" );
            }
        }

        // Add new versions

        Logger::info( sprintf(
            'Database migration from version %s to %s.',
            $from_version ?? 'none',
            Config::VERSION
        ) );
    }
}
