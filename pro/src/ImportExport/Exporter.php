<?php

declare(strict_types=1);

namespace OpinerMe\Pro\ImportExport;

defined( 'ABSPATH' ) || exit;

class Exporter {

    public function init(): void {
        add_action( 'admin_post_opiner_me_export_reviews', array( $this, 'export_reviews' ) );
        add_action( 'admin_post_nopriv_opiner_me_export_reviews', array( $this, 'no_access' ) );
    }

    public function export_reviews(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'No permission' );
        }

        if ( function_exists( 'set_time_limit' ) ) {
            // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
            @set_time_limit( 300 );
        }

        global $wpdb;

        $table   = $wpdb->prefix . 'om_opinions';

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
        $results = $wpdb->get_results( "SELECT * FROM `{$table}`" );
        // phpcs:enable

        $reviews = $results ?: array();
        $export  = array();

        foreach ( $reviews as $review ) {
            $post_slug = '';

            if ( ! empty( $review->post_id ) ) {
                $post = get_post( $review->post_id );

                if ( $post instanceof \WP_Post ) {
                    $post_slug = sanitize_title( $post->post_name );
                }
            }

            $export[] = array(
                'post_slug'       => $post_slug,
                'post_id'         => $review->post_id,
                'opinion_active'  => $review->opinion_active,
                'opinion_author'  => $review->opinion_author,
                'opinion_content' => $review->opinion_content,
                'opinion_rating'  => $review->opinion_rating,
                'opinion_ip'      => $review->opinion_ip,
                'opinion_date'    => $review->opinion_date
            );
        }

        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="opiner-me-reviews.json"' );

        echo wp_json_encode(
            $export,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }

    public function no_access(): void {
        wp_die( 'No access' );
    }
}
