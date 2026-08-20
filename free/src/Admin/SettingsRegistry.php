<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsRegistry {

    public function register(): void {
        $validator = new SettingsValidator;
        $renderer  = new FieldRenderer();

        register_setting(
            'opiner_me_options_group',
            'opiner_me_options',
            array(
                'sanitize_callback' => array( $validator, 'opiner_me_validate' )
            )
        );

        add_settings_section(
            'opiner_me_main_section',
            __( 'Main settings', 'opiner-me' ),
            null,
            'opiner-me-settings'
        );

        add_settings_field(
            'auto_approve',
            __( 'Automatically approve reviews', 'opiner-me' ),
            array( $renderer, 'render_auto_approve' ),
            'opiner-me-settings',
            'opiner_me_main_section'
        );

        add_settings_field(
            'opinions_per_page',
            __( 'Number of reviews per page', 'opiner-me' ),
            array( $renderer, 'render_opinions_per_page' ),
            'opiner-me-settings',
            'opiner_me_main_section'
        );

        add_settings_field(
            'min_length',
            __( 'Review min length', 'opiner-me' ),
            array( $renderer, 'render_min_length' ),
            'opiner-me-settings',
            'opiner_me_main_section'
        );

        add_settings_field(
            'max_length',
            __( 'Review max length', 'opiner-me' ),
            array( $renderer, 'render_max_length' ),
            'opiner-me-settings',
            'opiner_me_main_section'
        );

        add_settings_field(
            'blocked_words',
            __( 'Blocked words', 'opiner-me' ),
            array( $renderer, 'render_blocked_words' ),
            'opiner-me-settings',
            'opiner_me_main_section'
        );

        add_settings_field(
            'display_schema',
            __( 'Display JSON-LD Schema', 'opiner-me' ),
            array( $renderer, 'render_display_schema' ),
            'opiner-me-settings',
            'opiner_me_main_section'
        );

        do_action( 'opiner_me/settings/register_sections' );
    }
}
