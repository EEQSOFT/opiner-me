<?php

declare(strict_types=1);

namespace OpinerMe\Renderer;

defined( 'ABSPATH' ) || exit;

class RatingRenderer {

    public static function render_stars( float $average ): string {
        $average = round( $average * 2 ) / 2;

        $full_stars  = floor( $average );
        $half_star   = ( $average - $full_stars ) >= 0.5;
        $empty_stars = 5 - $full_stars - ( $half_star ? 1 : 0 );

        $output = '<span class="opiner-me-stars">';

        for ( $i = 0; $i < $full_stars; $i++ ) {
            $output .= '<span class="opiner-me-star opiner-me-star-full">★</span>';
        }

        if ( $half_star ) {
            $output .= '<span class="opiner-me-star opiner-me-star-half">★</span>';
        }

        for ( $i = 0; $i < $empty_stars; $i++ ) {
            $output .= '<span class="opiner-me-star opiner-me-star-empty">★</span>';
        }

        $output .= '</span>';

        return $output;
    }
}
