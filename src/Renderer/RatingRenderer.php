<?php

declare(strict_types=1);

namespace OpinerMe\Renderer;

defined( 'ABSPATH' ) || exit;

class RatingRenderer {

    public static function render_stars( float $average ): string {
        $filled = '⭐';
        $empty  = '';

        $output = '<div class="opiner-me-stars">';

        for ( $i = 1; $i <= 5; $i++ ) {
            $output .= ( $i <= round( $average ) ) ? $filled : $empty;
        }

        $output .= '</div>';

        return $output;
    }
}
