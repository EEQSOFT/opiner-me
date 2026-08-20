<?php

declare(strict_types=1);

namespace OpinerMe\Shortcode\Renderer;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\LoggerInterface;
use OpinerMe\Contracts\ShortcodeHandlerInterface;
use OpinerMe\Views\ViewLoader;

class FormShortcodeRenderer implements ShortcodeHandlerInterface {

    private LoggerInterface $logger;

    public function __construct( LoggerInterface $logger ) {
        $this->logger = $logger;
    }

    public function render( array $atts = array() ): string {
        if ( ! is_singular() ) {
            return '';
        }

        global $post;

        $atts = shortcode_atts( array(
            'post_id'  => $post->ID ?? 0,
            'redirect' => '',
            'theme'    => 'default',
        ), $atts, 'opiner_me_form' );

        $post_id = intval( $atts['post_id'] );

        if ( $post_id < 1 ) {
            $this->logger->warning( 'Form shortcode called without valid post ID.' );

            return '<div class="opiner-me-error">' . esc_html__( 'Invalid post ID.', 'opiner-me' ) . '</div>';
        }

        ob_start();

        $prefill = array(
            'om_author'  => '',
            'om_content' => '',
            'om_rating'  => 0
        );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe read-only usage of $_GET for feedback display
        $msg = sanitize_text_field( wp_unslash( $_GET['opiner_me_msg'] ?? '' ) );

        if ( ! empty( $msg ) ) {
            if ( $msg === 'error' ) {
                $unique = 'opiner_me_form_' . get_current_user_id();
                $saved = get_transient( $unique );

                if ( is_array( $saved['fields'] ?? '' ) ) {
                    $prefill = array_merge( $prefill, $saved['fields'] );
                }

                delete_transient( $unique );

                if ( is_wp_error( $saved['errors'] ?? '' ) ) {
                    echo '<div class="opiner-me-error">';

                    foreach ( $saved['errors']->get_error_messages() as $message ) {
                        echo esc_html( $message ) . '<br />';
                    }

                    echo '</div>';
                }
            }

            if ( $msg === 'success' ) {
                echo '<div class="opiner-me-success">' . nl2br( esc_html__( "Thank you for your opinion!\nYour review may require approval.", 'opiner-me' ) ) . '</div>';
            }
        }

        ViewLoader::load( 'form-shortcode-renderer', compact( 'post', 'prefill' ) );

        return ob_get_clean();
    }
}
