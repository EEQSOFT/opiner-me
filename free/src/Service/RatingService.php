<?php

declare(strict_types=1);

namespace OpinerMe\Service;

defined( 'ABSPATH' ) || exit;

class RatingService {

    public static function update( int $post_id ): void {
        global $wpdb;

        $opinions_table = esc_sql( $wpdb->prefix . 'om_opinions' );
        $ratings_table  = $wpdb->prefix . 'om_ratings';

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT opinion_rating FROM `{$opinions_table}` WHERE post_id = %d AND opinion_active = 1",
                $post_id
            )
        );
        // phpcs:enable

        if ( ! is_array( $results ) ) {
            $results = array();
        }

        $total = 0;
        $count = 0;

        foreach ( $results as $row ) {
            $rating = intval( $row->opinion_rating );

            if ( $rating >= 1 && $rating <= 5 ) {
                $total += $rating;
                $count++;
            }
        }

        $average = ( $count > 0 ) ? round( $total / $count, 2 ) : 0.00;

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe replace function on custom table, no caching needed
        $wpdb->replace(
            $ratings_table,

            array(
                'post_id'        => $post_id,
                'rating_average' => $average,
                'rating_count'   => $count,
                'rating_updated' => current_time( 'mysql' )
            ),

            array( '%d', '%f', '%d', '%s' )
        );
        // phpcs:enable
    }

    public static function get( int $post_id ): array {
        global $wpdb;

        $table      = $wpdb->prefix . 'om_ratings';
        $table_name = esc_sql( $table );

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT rating_average, rating_count FROM `{$table_name}` WHERE post_id = %d",
                $post_id
            )
        );
        // phpcs:enable

        return ( ! empty( $row ) ) ? array(
            'average' => floatval( $row->rating_average ),
            'count'   => intval( $row->rating_count ),
        ) : array( 'average' => 0.00, 'count' => 0 );
    }

    public static function delete( int $post_id ): void {
        global $wpdb;

        $table = $wpdb->prefix . 'om_ratings';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe delete function on custom table, no caching needed
        $wpdb->delete( $table, array( 'post_id' => $post_id ), array( '%d' ) );
    }
}
