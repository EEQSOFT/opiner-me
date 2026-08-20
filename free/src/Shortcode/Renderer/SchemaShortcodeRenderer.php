<?php

declare(strict_types=1);

namespace OpinerMe\Shortcode\Renderer;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\LoggerInterface;
use OpinerMe\Contracts\ShortcodeHandlerInterface;
use OpinerMe\Schema\SchemaBuilder;

class SchemaShortcodeRenderer implements ShortcodeHandlerInterface {

    private LoggerInterface $logger;

    public function __construct( LoggerInterface $logger ) {
        $this->logger = $logger;
    }

    public function render( array $atts = array() ): string {
        if ( ! is_singular() ) {
            return '';
        }

        global $wpdb;
        global $post;

        $atts = shortcode_atts( array(
            'post_id'         => $post->ID ?? 0,
            'type'            => 'Product',
            'include_reviews' => null,
        ), $atts, 'opiner_me_schema');

        $post_id         = intval( $atts['post_id'] );
        $include_reviews = filter_var( $atts['include_reviews'], FILTER_VALIDATE_BOOLEAN );

        if ( $post_id < 1 ) {
            $this->logger->warning( 'Schema shortcode called without valid post ID.' );

            return '';
        }

        if ( is_null( $atts['include_reviews'] ) ) {
            $options = get_option( 'opiner_me_options' );
            $schema  = strval( $options['display_schema'] ?? 'rating' );
        } elseif ( $include_reviews === true ) {
            $schema = 'list';
        } else {
            $schema = 'rating';
        }

        if ( $schema === 'none' ) {
            return '';
        }

        if ( $schema === 'rating' || $schema === 'list' ) {
            $title = get_the_title( $post );
            $url   = get_permalink( $post );

            $table      = $wpdb->prefix . 'om_ratings';
            $table_name = esc_sql( $table );

            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
            $rating = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT rating_average, rating_count FROM `{$table_name}` WHERE post_id = %d AND rating_count > 0",
                    $post_id
                )
            );
            // phpcs:enable

            if ( empty( $rating ) ) {
                return '';
            }

            $jsonld = SchemaBuilder::build_rating_schema( $atts['type'], $title, $url, $rating );
        }

        if ( ! isset( $jsonld ) ) {
            return '';
        }

        if ( $schema === 'list' ) {
            $table      = $wpdb->prefix . 'om_opinions';
            $table_name = esc_sql( $table );

            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
            $opinions = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM `{$table_name}` WHERE post_id = %d AND opinion_active = 1 ORDER BY opinion_date DESC LIMIT 3",
                    $post_id
                )
            );
            // phpcs:enable

            if ( ! is_array( $opinions ) ) {
                return '';
            }

            $jsonld = SchemaBuilder::add_reviews( $jsonld, $opinions );
        }

        return sprintf(
            '<script type="application/ld+json">%s</script>',
            wp_json_encode( $jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        );
    }
}
