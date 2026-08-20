<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;

class AssetsManager {

    public function register(): void {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets(): void {
        wp_enqueue_style(
            'opiner-me-style',
            OPINER_ME_URL . 'assets/css/style.css',
            array(),
            Config::ASSETS_VERSION
        );

        wp_enqueue_script( 'jquery' );

        wp_enqueue_script(
            'opiner-me-script',
            OPINER_ME_URL . 'assets/js/script.js',
            array( 'jquery' ),
            Config::ASSETS_VERSION,
            true
        );

        wp_localize_script( 'opiner-me-script', 'opiner_me_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );
    }
}
