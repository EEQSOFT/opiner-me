<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Helpers;

defined( 'ABSPATH' ) || exit;

class Utils {

    public static function init(): void {
        // PRO Helpers - if you need hooks
    }

    public static function sanitize( $value ) {
        return sanitize_text_field( $value );
    }
}
