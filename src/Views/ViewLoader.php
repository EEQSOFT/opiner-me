<?php

declare(strict_types=1);

namespace OpinerMe\Views;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Diagnostics\Logger;

class ViewLoader {

    public static function load( string $view, array $data = array() ): void {
        $view_path = OPINER_ME_PATH . '/views/' . $view . '.php';

        if ( ! file_exists( $view_path ) ) {
            Logger::error( sprintf( 'View %s not found at %s', $view, $view_path ) );

            return;
        }

        extract( $data, EXTR_SKIP );

        include $view_path;
    }
}
