<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class MenuManager {

    public function register(): void {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
    }

    public function add_menu(): void {
        add_menu_page(
            'Opiner Me - ' . __( 'Main Page', 'opiner-me' ),
            'Opiner Me',
            'manage_options',
            'opiner-me',
            null,
            'dashicons-star-filled'
        );

        add_submenu_page(
            'opiner-me',
            'Opiner Me - ' . __( 'Main Page', 'opiner-me' ),
            __( 'Main Page', 'opiner-me' ),
            'manage_options',
            'opiner-me',
            array( new MainPage(), 'render' )
        );

        add_submenu_page(
            'opiner-me',
            __( 'Plugin Settings', 'opiner-me' ),
            __( 'Settings', 'opiner-me' ),
            'manage_options',
            'opiner-me-settings',
            array( new SettingsPage(), 'render' )
        );

        add_submenu_page(
            'opiner-me',
            __( 'Opinion Moderation', 'opiner-me' ),
            __( 'Moderation', 'opiner-me' ),
            'manage_options',
            'opiner-me-moderation',
            array( new ModerationPage(), 'render' )
        );
    }
}
