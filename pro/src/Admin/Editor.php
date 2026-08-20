<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Admin;

defined( 'ABSPATH' ) || exit;

class Editor {

    public function init(): void {
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
    }

    public function register_menu(): void {
        add_submenu_page(
            'opiner-me',
            __( 'Opiner Me PRO', 'opiner-me' ),
            __( 'PRO Features', 'opiner-me' ),
            'manage_options',
            'opiner-me-pro',
            array( $this, 'render_page' )
        );
    }

    public function render_page(): void {
        echo '<div class="wrap"><h1>' . esc_html__( 'Opiner Me PRO', 'opiner-me' ) . '</h1></div>';
    }
}
