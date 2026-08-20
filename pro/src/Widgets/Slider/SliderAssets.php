<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Widgets\Slider;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;

class SliderAssets {

    public static function init(): void {
        add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
    }

    public static function enqueue(): void {
        if ( ! is_singular() ) {
            return;
        }

        global $post;

        if ( ! has_shortcode( $post->post_content, 'opiner_me_slider' ) ) {
            return;
        }

        wp_enqueue_style(
            'opiner-me-slider',
            OPINER_ME_URL . 'pro/assets/css/style-slider.css',
            array(),
            Config::ASSETS_VERSION
        );

        wp_enqueue_script(
            'opiner-me-slider',
            OPINER_ME_URL . 'pro/assets/js/script-slider.js',
            array(),
            Config::ASSETS_VERSION,
            true
        );
    }
}
