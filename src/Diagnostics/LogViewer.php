<?php

declare(strict_types=1);

namespace OpinerMe\Diagnostics;

defined( 'ABSPATH' ) || exit;

class LogViewer {

    public static function register(): void {
        add_action( 'admin_menu' , array( self::class, 'add_menu' ) );
    }

    public static function add_menu(): void {
        add_submenu_page(
            'options-general.php',
            'Opiner Me ' . __( 'Logs', 'opiner-me' ),
            'Opiner Me ' . __( 'Logs', 'opiner-me' ),
            'manage_options',
            'opiner-me-log-viewer',
            array( self::class, 'render' )
        );
    }

    public static function render(): void {
        $upload_dir = wp_upload_dir();
        $log_file   = trailingslashit( $upload_dir['basedir'] ) . 'opiner-me/log.txt';

        if ( isset( $_POST['opiner_me_clear_logs'] ) && check_admin_referer( 'opiner_me_clear_logs_action' ) ) {
            if ( file_exists( $log_file ) ) {
                file_put_contents( $log_file, '' );
                wp_delete_file( $log_file );

                echo '<div class="updated"><p>' . esc_html__( 'Logs have been cleared.', 'opiner-me' ) . '</p></div>';
            }
        }

        echo '<div class="wrap"><h1>Opiner Me ' . esc_html__( 'Logs', 'opiner-me' ) . '</h1>';
        echo '<form method="post">';

        wp_nonce_field( 'opiner_me_clear_logs_action' );

        echo '<p><input type="submit" name="opiner_me_clear_logs" class="button button-secondary" value="' . esc_html__( 'Clear logs', 'opiner-me' ) . '"></p>';
        echo '</form>';
        echo '<pre style="max-height: 600px; padding: 12px; border: 1px solid #ccc; background: #fff; overflow: auto;">';

        if ( file_exists( $log_file ) ) {
            echo esc_html( file_get_contents( $log_file ) );
        } else {
            echo esc_html__( 'No log file.', 'opiner-me' );
        }

        echo '</pre></div>';
    }
}
