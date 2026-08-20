<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;

class LicenseManager {

    const OPTION_KEY       = 'opiner_me_license_key';
    const OPTION_STATE     = 'opiner_me_license_status';
    const OPTION_EXPIRES   = 'opiner_me_license_expires';
    const OPTION_PLAN      = 'opiner_me_license_plan';
    const OPTION_MAX_SITES = 'opiner_me_license_max_sites';
    const CRON_HOOK        = 'opiner_me_check_license_cron';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_license_page' ) );
        add_action( 'admin_init', array( $this, 'maybe_handle_form' ) );
        add_action( self::CRON_HOOK, array( $this, 'cron_check_license' ) );

        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time() + 300, 'daily', self::CRON_HOOK );
        }
    }

    public function add_license_page(): void {
        add_submenu_page(
            'opiner-me',
            __( 'License PRO', 'opiner-me' ),
            __( 'License PRO', 'opiner-me' ),
            'manage_options',
            'opiner-me-license',
            array( $this, 'render_page' )
        );
    }

    public function render_page(): void {
        $license_key = get_option( self::OPTION_KEY, '' );
        $status      = get_option( self::OPTION_STATE, 'unknown' );
        $expires     = get_option( self::OPTION_EXPIRES, '' );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'License PRO', 'opiner-me' ); ?></h1>

            <form method="post">
                <?php wp_nonce_field( 'opiner_me_license_save', 'opiner_me_license_nonce' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="opiner_me_license_key"><?php esc_html_e( 'License key', 'opiner-me' ); ?></label>
                        </th>

                        <td>
                            <input type="text" id="opiner_me_license_key" name="opiner_me_license_key"
                                   value="<?php echo esc_attr( $license_key ); ?>" class="regular-text" />

                            <p class="description">
                                <?php esc_html_e( 'Enter the key you received after purchasing Opiner Me PRO.', 'opiner-me' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button( __( 'Save and verify', 'opiner-me' ) ); ?>
            </form>

            <h2><?php esc_html_e( 'License status', 'opiner-me' ); ?></h2>

            <p><strong><?php echo esc_html( ucfirst( $status ) ); ?></strong></p>

            <?php if ( $expires ) { ?>
                <p>
                    <?php
                    /* translators: %s: License expiration date */
                    printf( esc_html__( 'Expires: %s', 'opiner-me' ), esc_html( $expires ) );
                    ?>
                </p>
            <?php } ?>

            <div><br /><a href="https://opiner.me/buy-pro" class="button" target="_blank"><?php esc_html_e( 'Buy', 'opiner-me' ); ?> Opiner Me PRO</a></div>
        </div>
        <?php
    }

    public function maybe_handle_form(): void {
        if ( ! isset( $_POST['opiner_me_license_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['opiner_me_license_nonce'] ) ),
            'opiner_me_license_save'
        ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $license_key = isset( $_POST['opiner_me_license_key'] )
            ? trim( sanitize_text_field( wp_unslash( $_POST['opiner_me_license_key'] ) ) )
            : '';

        update_option( self::OPTION_KEY, $license_key );

        $result = $this->verify_license( $license_key );

        if ( ! isset( $result['status'] ) ) {
            update_option( self::OPTION_STATE, 'error' );
            delete_option( self::OPTION_EXPIRES );
            wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

            exit;
        }

        if ( $result['status'] === 'site_limit_reached' ) {
            update_option( self::OPTION_STATE, 'site_limit_reached' );
            delete_option( self::OPTION_EXPIRES );
            wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

            exit;
        }

        if ( $result['status'] === 'invalid' ) {
            update_option( self::OPTION_STATE, 'invalid' );
            delete_option( self::OPTION_EXPIRES );
            wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

            exit;
        }

        if ( $result['status'] === 'expired' ) {
            update_option( self::OPTION_STATE, 'expired' );

            if ( isset( $result['expires'] ) ) {
                update_option( self::OPTION_EXPIRES, $result['expires'] );
            }

            wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

            exit;
        }

        if ( $result['status'] === 'trial' ) {
            update_option( self::OPTION_STATE, 'trial' );

            if ( isset( $result['expires'] ) ) {
                update_option( self::OPTION_EXPIRES, $result['expires'] );
            }

            wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

            exit;
        }

        if ( $result['status'] === 'valid' ) {
            update_option( self::OPTION_STATE, 'valid' );

            if ( isset( $result['plan'] ) ) {
                update_option( self::OPTION_PLAN, $result['plan'] );
            }

            if ( isset( $result['max_sites'] ) ) {
                update_option( self::OPTION_MAX_SITES, $result['max_sites'] );
            }

            if ( isset( $result['expires'] ) ) {
                update_option( self::OPTION_EXPIRES, $result['expires'] );
            }

            wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

            exit;
        }

        update_option( self::OPTION_STATE, $result['status'] );
        delete_option( self::OPTION_EXPIRES );
        wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-license' ) );

        exit;
    }

    public function verify_license( string $license_key ): array {
        if ( $license_key === '' ) {
            delete_option( self::OPTION_EXPIRES );

            return array( 'status' => 'empty' );
        }

        $response = wp_remote_post( Config::LICENSE_API, array(
            'timeout' => 10,
            'headers' => array(
                'Content-Type'   => 'application/json'
            ),
            'body'    => wp_json_encode( array(
                'license_key'    => $license_key,
                'site_url'       => home_url(),
                'plugin_version' => Config::VERSION
            ) )
        ) );

        if ( is_wp_error( $response ) ) {
            delete_option( self::OPTION_EXPIRES );

            return array( 'status' => 'error' );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! is_array( $data ) || empty( $data['status'] ) ) {
            delete_option( self::OPTION_EXPIRES );

            return array( 'status' => 'error' );
        }

        return $data;
    }

    public function cron_check_license(): void {
        $license_key = get_option( self::OPTION_KEY, '' );

        if ( $license_key === '' ) {
            return;
        }

        $result = $this->verify_license( $license_key );

        if ( ! isset( $result['status'] ) ) {
            return;
        }

        if ( $result['status'] === 'site_limit_reached' ) {
            update_option( self::OPTION_STATE, 'site_limit_reached' );
            delete_option( self::OPTION_EXPIRES );

            return;
        }

        if ( $result['status'] === 'invalid' ) {
            update_option( self::OPTION_STATE, 'invalid' );
            delete_option( self::OPTION_EXPIRES );

            return;
        }

        if ( $result['status'] === 'expired' ) {
            update_option( self::OPTION_STATE, 'expired' );

            if ( isset( $result['expires'] ) ) {
                update_option( self::OPTION_EXPIRES, $result['expires'] );
            }

            return;
        }

        if ( $result['status'] === 'trial' ) {
            update_option( self::OPTION_STATE, 'trial' );

            if ( isset( $result['expires'] ) ) {
                update_option( self::OPTION_EXPIRES, $result['expires'] );
            }

            return;
        }

        if ( $result['status'] === 'valid' ) {
            update_option( self::OPTION_STATE, 'valid' );

            if ( isset( $result['plan'] ) ) {
                update_option( self::OPTION_PLAN, $result['plan'] );
            }

            if ( isset( $result['max_sites'] ) ) {
                update_option( self::OPTION_MAX_SITES, $result['max_sites'] );
            }

            if ( isset( $result['expires'] ) ) {
                update_option( self::OPTION_EXPIRES, $result['expires'] );
            }

            return;
        }
    }

    public static function is_pro_active(): bool {
        return get_option( self::OPTION_STATE ) === 'valid';
    }

    public static function deactivate_plugin(): void {
        $license_key = get_option( self::OPTION_KEY, '' );

        if ( $license_key === '' ) {
            return;
        }

        $response = wp_remote_post( Config::LICENSE_API_DEACTIVATE, array(
            'timeout' => 10,
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body'    => wp_json_encode( array(
                'license_key'  => $license_key,
                'site_url'     => home_url()
            ) )
        ) );

        delete_option( self::OPTION_STATE );
        delete_option( self::OPTION_EXPIRES );
    }
}
