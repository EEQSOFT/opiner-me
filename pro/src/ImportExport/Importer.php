<?php

declare(strict_types=1);

namespace OpinerMe\Pro\ImportExport;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Diagnostics\Logger;

class Importer {

    public function init(): void {
        add_action( 'admin_post_opiner_me_import_reviews', array( $this, 'import_reviews' ) );
        add_action( 'admin_post_nopriv_opiner_me_import_reviews', array( $this, 'no_access' ) );
    }

    public function import_reviews(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'No permission' );
        }

        if ( function_exists( 'set_time_limit' ) ) {
            // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
            @set_time_limit( 0 );
        }

        // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
        @ini_set( 'memory_limit', '512M' );

        check_admin_referer( 'opiner_me_import_reviews', 'opiner_me_import_nonce' );

        if ( empty( $_FILES['opiner_me_import']['tmp_name'] ) ) {
            wp_die( 'No file uploaded' );
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $json = file_get_contents( $_FILES['opiner_me_import']['tmp_name'] );
        $data = json_decode( $json, true );

        if ( ! isset( $data[0] ) || ! is_array( $data[0] ) ) {
            wp_die( 'Invalid JSON file' );
        }

        global $wpdb;

        $table   = $wpdb->prefix . 'om_opinions';
        $added   = 0;
        $skipped = 0;

        foreach ( $data as $review ) {
            $slug = sanitize_title( $review['post_slug'] ?? '' );

            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
            $post_id = $slug
                ? intval( $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT ID FROM `{$wpdb->posts}`
                        WHERE post_name = %s
                        AND post_status = 'publish'
                        AND post_type IN ('post', 'page')
                        ORDER BY ID DESC
                        LIMIT 1",
                        $slug
                    )
                ) )
                : 0;
            // phpcs:enable

            $active  = intval( $review['opinion_active'] ?? 0 );
            $author  = sanitize_text_field( $review['opinion_author'] ?? '' );
            $content = sanitize_textarea_field( $review['opinion_content'] ?? '' );
            $rating  = intval( $review['opinion_rating'] ?? 0 );
            $ip      = sanitize_text_field( $review['opinion_ip'] ?? '' );
            $date    = sanitize_text_field( $review['opinion_date'] ?? current_time( 'mysql' ) );

            if ( $rating < 1 || $rating > 5 || empty( $content ) ) {
                $skipped++;

                continue;
            }

            if ( $this->exists( $ip, $date ) ) {
                $skipped++;

                continue;
            }

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $inserted = $wpdb->insert(
                $table,

                array(
                    'post_id'         => $post_id,
                    'opinion_active'  => $active,
                    'opinion_author'  => $author,
                    'opinion_content' => $content,
                    'opinion_rating'  => $rating,
                    'opinion_ip'      => $ip,
                    'opinion_date'    => $date
                ),

                array( '%d', '%d', '%s', '%s', '%d', '%s', '%s' )
            );

            if ( $inserted ) {
                $added++;
            }

            if ( empty( $inserted ) ) {
                Logger::error( sprintf(
                    'Import failed for post_id %d: %s | Query: %s',
                    $post_id ?? 0,
                    $wpdb->last_error,
                    $wpdb->last_query
                ) );
            }
        }

        wp_safe_redirect( admin_url(
            'admin.php?page=opiner-me-settings'
            . '&import=success'
            . '&added=' . intval( $added ?? 0 )
            . '&skipped=' . intval( $skipped ?? 0 )
        ) );

        exit;
    }

    public function no_access(): void {
        wp_die( 'No access' );
    }

    private function exists( string $ip, string $date ): bool {
        global $wpdb;

        $table = $wpdb->prefix . 'om_opinions';

        // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $sql = $wpdb->prepare(
            "SELECT 1 FROM `{$table}` WHERE opinion_ip = %s AND opinion_date = %s LIMIT 1",
            $ip,
            $date
        );

        $var = $wpdb->get_var( $sql );
        // phpcs:enable

        return $var > 0;
    }
}
