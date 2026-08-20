<?php
/**
 * Plugin Name:  Opiner Me
 * Plugin URI:   https://opiner.me
 * Description:  Simple star rating & opinions plugin with frontend form, admin panel, and JSON-LD Schema.
 * Author:       EEQSOFT
 * Author URI:   https://www.eeqsoft.com
 * Version:      1.0.0
 * Requires PHP: 8.0
 * License:      GPLv2 or later
 * Text Domain:  opiner-me
 * Domain Path:  /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'OPINER_ME_URL', plugin_dir_url( __FILE__ ) );
define( 'OPINER_ME_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPINER_ME_FILE', __FILE__ );
define( 'OPINER_ME_DIR', __DIR__ );

spl_autoload_register(
    function ( $class ) {
        $prefix   = 'OpinerMe\\';
        $base_dir = OPINER_ME_DIR . '/src/';
        $length   = strlen( $prefix );

        if ( strncmp( $prefix, $class, $length ) !== 0 ) {
            return;
        }

        $relative_class = substr( $class, $length );
        $file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

        if ( file_exists( $file ) ) {
            require $file;
        }
    }
);

new OpinerMe\Plugin\Plugin();
