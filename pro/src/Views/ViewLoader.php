<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Views;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Views\ViewLoader as FreeViewLoader;

class ViewLoader extends FreeViewLoader {

    public static function load( string $view, array $data = array() ): void {
        $pro_path = OPINER_ME_PATH . 'pro/views/' . $view . '.php';

        if ( file_exists( $pro_path ) ) {
            extract( $data, EXTR_SKIP );

            include $pro_path;

            return;
        }

        parent::load( $view, $data );
    }
}
