<?php

declare(strict_types=1);

namespace OpinerMe\Free;

defined( 'ABSPATH' ) || exit;

class Loader {

    public function init(): void {
        $this->register_autoloader();

        new \OpinerMe\Plugin\Plugin();
    }

    public function register_only_autoloader(): void {
        $this->register_autoloader();
    }

    private function register_autoloader(): void {
        spl_autoload_register( function( $class ) {
            $prefix   = 'OpinerMe\\';
            $base_dir = __DIR__ . '/src/';
            $length   = strlen( $prefix );

            if ( strncmp( $prefix, $class, $length ) !== 0 ) {
                return;
            }

            $relative_class = substr( $class, $length );
            $file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

            if ( file_exists( $file ) ) {
                require $file;
            }
        } );
    }
}
