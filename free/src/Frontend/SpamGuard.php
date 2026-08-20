<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;
use OpinerMe\Diagnostics\Logger;
use OpinerMe\Utils\RequestHelper;

class SpamGuard {

    private const SPAM_LIMIT_5_MINUTES = 1;
    private const SPAM_LIMIT_GLOBAL    = 10;
    private const SPAM_LIMIT_POST      = 1;
    private ?Logger $logger;

    public function __construct( ?Logger $logger = null ) {
        $this->logger = $logger;
    }

    public function check( int $post_id ): void {
        global $wpdb;

        $table            = $wpdb->prefix . 'om_opinions';
        $table_name       = esc_sql( $table );
        $ip               = RequestHelper::get_user_ip();
        $now              = current_time( 'mysql' );
        $five_minutes_ago = gmdate( 'Y-m-d H:i:s', strtotime( $now ) - 5 * 60 );
        $one_day_ago      = gmdate( 'Y-m-d H:i:s', strtotime( $now ) - 24 * 60 * 60 );
        $message          = '';

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $recent_opinion = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM `{$table_name}` WHERE opinion_ip = %s AND opinion_date >= %s",
                $ip,
                $five_minutes_ago
            )
        );
        // phpcs:enable

        if ( $recent_opinion >= self::SPAM_LIMIT_5_MINUTES ) {
            $message     = 'Please wait 5 minutes before adding another review.';
            $translation = esc_html__( 'Please wait 5 minutes before adding another review.', 'opiner-me' );
        }

        if ( empty( $message ) ) {
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
            $all_opinions = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM `{$table_name}` WHERE opinion_ip = %s AND opinion_date >= %s",
                    $ip,
                    $one_day_ago
                )
            );
            // phpcs:enable

            if ( $all_opinions >= self::SPAM_LIMIT_GLOBAL ) {
                $message     = 'You have exceeded the limit of opinions added.';
                $translation = esc_html__( 'You have exceeded the limit of opinions added.', 'opiner-me' );
            }
        }

        if ( empty( $message ) ) {
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
            $post_opinions = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM `{$table_name}` WHERE post_id = %d AND opinion_ip = %s AND opinion_date >= %s",
                    $post_id,
                    $ip,
                    $one_day_ago
                )
            );
            // phpcs:enable

            if ( $post_opinions >= self::SPAM_LIMIT_POST ) {
                $message     = 'You can only post one review per day for this post.';
                $translation = esc_html__( 'You can only post one review per day for this post.', 'opiner-me' );
            }
        }

        if ( ! empty( $message ) ) {
            if ( Config::LOG_SPAM_GUARD ) {
                $this->logger?->error( sprintf(
                    'Anti-spam error for post_id %d, ip %s, date %s: %s',
                    $post_id,
                    $ip,
                    $now,
                    $message
                ) );
            }

            wp_die( esc_html( $translation ), esc_html__( 'Spam protection', 'opiner-me' ), 403 );
        }
    }
}
