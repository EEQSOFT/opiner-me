<?php

declare(strict_types=1);

namespace OpinerMe\Cleanup;

defined( 'ABSPATH' ) || exit;

class Uninstaller {

    public static function run(): void {
        self::delete_options();
        self::delete_transients();
        self::delete_logs();
        // self::drop_tables();
    }

    private static function delete_options(): void {
        $options = array(
            // 'opiner_me_db_version',
            // 'opiner_me_options'
        );

        foreach ( $options as $key ) {
            delete_option( $key );
        }
    }

    private static function delete_transients(): void {
        $transients = array(
            'opiner_me_form_' . get_current_user_id(),
            'opiner_me_notice'
        );

        foreach ( $transients as $key ) {
            delete_transient( $key );
        }
    }

    private static function delete_logs(): void {
        $upload_dir = wp_upload_dir();
        $log_file   = trailingslashit( $upload_dir['basedir'] ) . 'opiner-me/log.txt';
        $log_dir    = dirname( $log_file );

        if ( file_exists( $log_file ) ) {
            wp_delete_file( $log_file );
        }

        global $wp_filesystem;

        if ( ! $wp_filesystem ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';

            WP_Filesystem();
        }

        $files = array_diff( scandir( $log_dir ), array( '.', '..' ) );

        if ( is_dir( $log_dir ) && empty( $files ) ) {
            $wp_filesystem->rmdir( $log_dir );
        }
    }
}
