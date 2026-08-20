<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsProValidator {

    public function __construct() {
        add_filter( 'opiner_me/options/validate', array( $this, 'validate_pro_fields' ), 10, 2 );
    }

    public function validate_pro_fields( array $options, array $input ): array {
        $options['notify_enabled'] = isset( $input['notify_enabled'] ) ? 1 : 0;

        if ( isset( $input['notify_email'] ) ) {
            $email = sanitize_email( $input['notify_email'] );

            if ( empty( $email ) || ! is_email( $email ) ) {
                add_settings_error(
                    'opiner_me_options',
                    'opiner_me_pro_invalid_email',
                    __( 'The PRO notification email address is invalid.', 'opiner-me' ),
                    'error'
                );

                $options['notify_email'] = get_option( 'admin_email' );
            } else {
                $options['notify_email'] = $email;
            }
        }

        return $options;
    }
}
