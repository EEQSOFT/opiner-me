<?php

declare(strict_types=1);

namespace OpinerMe\Utils;

defined( 'ABSPATH' ) || exit;

class RequestHelper {

    public static function get_user_ip(): string {
        $ip = '';

        if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $xff = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
            $ip  = explode( ',', $xff )[0];
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        return trim( $ip );
    }
}
