<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsPage {

    public function render(): void {
        echo '<div class="wrap opiner-me-admin-container">';
        echo '<h1>' . esc_html__( 'Plugin Settings', 'opiner-me' )  . '</h1>';

        if (
            class_exists( '\OpinerMe\Pro\Admin\FieldRenderer' )
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            && isset( $_GET['import'] )
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            && ! isset( $_GET['settings-updated'] )
        ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( $_GET['import'] === 'success' ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $added   = isset( $_GET['added'] ) ? intval( $_GET['added'] ) : 0;
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $skipped = isset( $_GET['skipped'] ) ? intval( $_GET['skipped'] ) : 0;

                if ( $added > 0 ) {
                    echo '<div class="notice notice-success is-dismissible"><p>'
                        . sprintf(
                            /* translators: %d: Reviews added */
                            esc_html__( 'Successfully imported %d reviews.', 'opiner-me' ),
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            $added
                        )
                        . '</p></div>';
                }

                if ( $skipped > 0 ) {
                    echo '<div class="notice notice-warning is-dismissible"><p>'
                        . sprintf(
                            /* translators: %d: Reviews skipped */
                            esc_html__( 'Skipped %d duplicate reviews.', 'opiner-me' ),
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            $skipped
                        )
                        . '</p></div>';
                }

                if ( $added === 0 && $skipped === 0 ) {
                    echo '<div class="notice notice-info is-dismissible"><p>'
                        . esc_html__( 'Import completed, but no reviews were added.', 'opiner-me' )
                        . '</p></div>';
                }
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( $_GET['import'] === 'error' ) {
                echo '<div class="notice notice-error is-dismissible"><p>'
                    . esc_html__( 'Import failed. Please check the JSON file.', 'opiner-me' )
                    . '</p></div>';
            }
        }

        settings_errors( 'opiner_me_options' );

        echo '<form method="post" action="options.php">';

        settings_fields( 'opiner_me_options_group' );
        do_settings_sections( 'opiner-me-settings' );
        submit_button();

        echo '</form>';

        if ( class_exists( '\OpinerMe\Pro\Admin\FieldRenderer' ) ) {
            $renderer = new \OpinerMe\Pro\Admin\FieldRenderer();

            echo '<br /><hr />';
            echo '<h2>' . esc_html__( 'Import / Export reviews (PRO)', 'opiner-me' ) . '</h2>';
            echo '<br />';

            $renderer->render_import_reviews_form();
            $renderer->render_export_reviews_button();
        }

        echo '<script>
            if (window.location.search.includes("import=")) {
                const url = new URL(window.location.href);
                url.searchParams.delete("import");
                url.searchParams.delete("added");
                url.searchParams.delete("skipped");
                window.history.replaceState({}, "", url.toString());
            }
        </script>';

        echo '</div>';
    }
}
