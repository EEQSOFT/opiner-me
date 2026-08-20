<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsPanel {

    private array $settings;

    public function __construct( array $settings = array() ) {
        $this->settings = $settings;
    }

    public function register(): void {
        $registry = new SettingsRegistry();

        add_action( 'admin_init', array( $registry, 'register' ) );
    }
}
