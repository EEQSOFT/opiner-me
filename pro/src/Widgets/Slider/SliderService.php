<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Widgets\Slider;

defined( 'ABSPATH' ) || exit;

class SliderService {

    public static function get_opinions( int $limit, int $post_id = 0 ): array {
        global $wpdb;

        $table  = $wpdb->prefix . 'om_opinions';
        $sql    = "SELECT * FROM `{$table}` WHERE opinion_active = 1";
        $params = array();

        if ( $post_id > 0 ) {
            $sql     .= ' AND post_id = %d';
            $params[] = $post_id;
        }

        $sql     .= ' ORDER BY opinion_date DESC LIMIT %d';
        $params[] = $limit;

        // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
        $results = $wpdb->get_results(
            $wpdb->prepare( $sql, $params )
        );
        // phpcs:enable

        return $results ?: array();
    }

    public static function shorten( string $text, int $max_words ): string {
        $words = explode( ' ', wp_strip_all_tags( $text ) );

        if ( count( $words ) > $max_words ) {
            $words = array_slice( $words, 0, $max_words );

            return implode( ' ', $words ) . ' (...)';
        }

        return implode( ' ', $words );
    }
}
