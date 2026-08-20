<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;

class AssetsManager {

    public function register(): void {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    public function enqueue_admin_assets( string $hook ): void {
        $allowed_hooks = array(
            'toplevel_page_opiner-me',
            'opiner-me_page_opiner-me-settings',
            'opiner-me_page_opiner-me-moderation'
        );

        if ( ! in_array( $hook, $allowed_hooks, true ) ) {
            return;
        }

        wp_enqueue_style(
            'opiner-me-admin-style',
            OPINER_ME_URL . 'assets/css/style-admin.css',
            array(),
            Config::ASSETS_VERSION
        );

        wp_enqueue_script(
            'opiner-me-admin-script',
            OPINER_ME_URL . 'assets/js/script-admin.js',
            array(),
            Config::ASSETS_VERSION,
            true
        );
    }
}
