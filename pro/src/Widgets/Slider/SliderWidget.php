<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Widgets\Slider;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Pro\Views\ViewLoader;

class SliderWidget {

    public function init(): void {
        add_shortcode( 'opiner_me_slider', array( $this, 'render' ) );
    }

    public function render( array $atts = array() ): string {
        if ( ! is_singular() ) {
            return '';
        }

        $atts = shortcode_atts( array(
            'post_id'   => 0,
            'limit'     => 10,
            'autoplay'  => 'true',
            'speed'     => 4000,
            'arrows'    => 'true',
            'dots'      => 'true',
            'fade'      => 'false',
            'max_words' => 30
        ), $atts );

        $post_id   = intval( $atts['post_id'] );
        $limit     = intval( $atts['limit'] );
        $autoplay  = $atts['autoplay'] === 'true';
        $speed     = intval( $atts['speed'] );
        $arrows    = $atts['arrows'] === 'true';
        $dots      = $atts['dots'] === 'true';
        $fade      = $atts['fade'] === 'true';
        $max_words = intval( $atts['max_words'] );

        $opinions = SliderService::get_opinions( $limit, $post_id );

        if ( empty( $opinions ) ) {
            return '<p>' . esc_html__( 'No reviews.', 'opiner-me' ) . '</p>';
        }

        $data = array(
            'opinions'  => $opinions,
            'autoplay'  => $autoplay,
            'speed'     => $speed,
            'arrows'    => $arrows,
            'dots'      => $dots,
            'fade'      => $fade,
            'max_words' => $max_words,
        );

        ob_start();

        ViewLoader::load( 'slider', $data );

        return ob_get_clean();
    }
}
