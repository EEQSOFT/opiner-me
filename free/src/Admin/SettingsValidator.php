<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsValidator {

    public function opiner_me_validate( array $input ): array {
        $valid = array();

        $valid['auto_approve'] = ! empty( $input['auto_approve'] ) ? 1 : 0;

        $opinions_per_page = absint( $input['opinions_per_page'] ?? 0 );

        if ( $opinions_per_page >= 1 && $opinions_per_page <= 1000 ) {
            $valid['opinions_per_page'] = $opinions_per_page;
        } else {
            add_settings_error(
                'opiner_me_options',
                'invalid_opinions_per_page',
                __( 'The number of reviews must be between 1 and 1000.', 'opiner-me' )
            );
        }

        $min_length = absint( $input['min_length'] ?? 0 );
        $max_length = absint( $input['max_length'] ?? 0 );

        if ( $min_length >= 10 && $max_length <= 1000 && $min_length < $max_length ) {
            $valid['min_length'] = $min_length;
            $valid['max_length'] = $max_length;
        } else {
            if ( $min_length >= $max_length ) {
                add_settings_error(
                    'opiner_me_options',
                    'invalid_min_max_length',
                    __( 'The review min length must be shorter than the review max length.', 'opiner-me' )
                );
            } else {
                add_settings_error(
                    'opiner_me_options',
                    'invalid_min_max_length',
                    __( 'The length of reviews must be between 10 and 1000.', 'opiner-me' )
                );
            }
        }

        if ( isset( $input['blocked_words'] ) && strlen( trim( $input['blocked_words'] ) ) >= 0 ) {
            $valid['blocked_words'] = sanitize_textarea_field( $input['blocked_words'] );
        } else {
            add_settings_error(
                'opiner_me_options',
                'invalid_blocked_words',
                __( 'Enter the blocked words.', 'opiner-me' )
            );
        }

        $allowed_schema = array( 'none', 'rating', 'list' );
        $schema = sanitize_text_field( $input['display_schema'] ?? '' );

        if ( in_array( $schema, $allowed_schema, true ) ) {
            $valid['display_schema'] = $schema;
        } else {
            add_settings_error(
                'opiner_me_options',
                'invalid_display_schema',
                __( 'Select JSON-LD Schema.', 'opiner-me' )
            );
        }

        $valid = apply_filters( 'opiner_me/options/validate', $valid, $input );

        if ( count( get_settings_errors( 'opiner_me_options' ) ) > 0 ) {
            return get_option( 'opiner_me_options', array() );
        }

        add_settings_error(
            'opiner_me_options',
            'settings_saved',
            __( 'Settings saved.', 'opiner-me' ),
            'updated'
        );

        return $valid;
    }
}
