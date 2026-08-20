<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

class AjaxController {

    public function register(): void {
        add_action( 'wp_ajax_opiner_me_load_more', array( $this, 'load_more_opinions' ) );
        add_action( 'wp_ajax_nopriv_opiner_me_load_more', array( $this, 'load_more_opinions' ) );
    }

    public function load_more_opinions(): void {
        global $wpdb;

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Safe read-only AJAX, no data modification
        $post_id = intval( $_POST['post_id'] ?? 0 );

        if ( $post_id === 0 ) {
            wp_send_json_error( __( 'No valid post ID', 'opiner-me' ) );
        }

        $post = get_post( $post_id );

        if ( empty( $post ) ) {
            wp_send_json_error( __( 'Post not found', 'opiner-me' ) );
        }

        $table      = $wpdb->prefix . 'om_opinions';
        $table_name = esc_sql( $table );
        $options    = get_option( 'opiner_me_options' );
        $limit      = intval( $options['opinions_per_page'] ?? 10 );

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Safe read-only AJAX, no data modification
        $offset = intval( $_POST['offset'] ?? 0 );

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $opinions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `{$table_name}` WHERE post_id = %d AND opinion_active = 1 ORDER BY opinion_date DESC LIMIT %d OFFSET %d",
                $post_id,
                $limit,
                $offset
            )
        );
        // phpcs:enable

        if ( ! is_array( $opinions ) ) {
            wp_die();
        }

        foreach ( $opinions as $op ) {
            $html  = '<div class="opiner-me-opinion">';
            $html .= '<strong>' . esc_html( $op->opinion_author ) . '</strong>';
            $html .= '<span class="opiner-me-stars"> ' . esc_html( str_repeat( '⭐', intval( $op->opinion_rating ) ) ) . '</span>';
            $html .= '<span> (' . intval( $op->opinion_rating ) . ')</span>';
            $html .= '<p>' . nl2br( esc_html( $op->opinion_content ) ) . '</p>';
            $html .= '<small>' . esc_html( $op->opinion_date ) . '</small>';
            $html .= '</div>';

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All output escaped during HTML construction
            echo $html;
        }

        wp_die();
    }
}
