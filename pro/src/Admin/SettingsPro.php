<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsPro {

    public function __construct() {
        add_action( 'opiner_me/settings/register_sections', array( $this, 'register_settings' ) );
    }

    public function register_settings(): void {
        $renderer  = new FieldRenderer();

        add_settings_section(
            'opiner_me_pro_notifications',
            __( 'Email notifications (PRO)', 'opiner-me' ),
            null,
            'opiner-me-settings'
        );

        add_settings_field(
            'notify_enabled',
            __( 'Turn on notifications', 'opiner-me' ),
            array( $renderer, 'render_enabled_field' ),
            'opiner-me-settings',
            'opiner_me_pro_notifications'
        );

        add_settings_field(
            'notify_email',
            __( "Administrator's email address", 'opiner-me' ),
            array( $renderer, 'render_email_field' ),
            'opiner-me-settings',
            'opiner_me_pro_notifications'
        );
    }
}
