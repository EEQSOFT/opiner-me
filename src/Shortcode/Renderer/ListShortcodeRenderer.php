<?php

declare(strict_types=1);

namespace OpinerMe\Shortcode\Renderer;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\LoggerInterface;
use OpinerMe\Contracts\ShortcodeHandlerInterface;

class ListShortcodeRenderer implements ShortcodeHandlerInterface {

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
            'post_id' => $post->ID ?? 0,
            'limit'   => 0,
            'order'   => 'desc',
            'theme'   => 'default',
        ), $atts, 'opiner_me_list' );

        $post_id = intval( $atts['post_id'] );
        $limit   = intval( $atts['limit'] );
        $order   = ( strtolower( $atts['order'] ) === 'asc' ) ? 'ASC' : 'DESC';

        if ( $post_id < 1 ) {
            $this->logger->warning( 'List shortcode called without valid post ID.' );

            return '<div class="opiner-me-error">' . esc_html__( 'Invalid post ID.', 'opiner-me' ) . '</div>';
        }

        $table      = $wpdb->prefix . 'om_opinions';
        $table_name = esc_sql( $table );
        $options    = get_option( 'opiner_me_options' );
        $limit      = ( $limit < 1 ) ? intval( $options['opinions_per_page'] ?? 10 ) : $limit;

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe read-only usage of $_GET to control opinion display count
        $show = intval( $_GET['show'] ?? $limit );
        $show = max( $limit, $show );

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $total = intval( $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM `{$table_name}` WHERE post_id = %d AND opinion_active = 1",
                $post_id
            )
        ) );
        // phpcs:enable

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter -- ORDER BY direction safely validated ('ASC' or 'DESC')
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $opinions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `{$table_name}` WHERE post_id = %d AND opinion_active = 1 ORDER BY opinion_date {$order} LIMIT %d",
                $post_id,
                $show
            )
        );
        // phpcs:enable

        if ( ! is_array( $opinions ) ) {
            return '';
        }

        ob_start();

        echo '<div id="opiner-me-list" class="opiner-me-list">';

        foreach ( $opinions as $op ) {
            echo '<div class="opiner-me-opinion">';
            echo '<strong>' . esc_html( wp_unslash( $op->opinion_author ) ) . '</strong>';
            echo '<span class="opiner-me-stars"> ' . esc_html( str_repeat( '⭐', intval( $op->opinion_rating ) ) ) . '</span>';
            echo '<span> (' . intval( $op->opinion_rating ) . ')</span>';
            echo '<p>' . nl2br( esc_html( wp_unslash( $op->opinion_content ) ) ) . '</p>';
            echo '<small>' . esc_html( $op->opinion_date ) . '</small>';
            echo '</div>';
        }

        echo '</div>';

        if ( $show < $total ) {
            ?>
            <div>
                <a
                    href="<?php echo esc_url( add_query_arg( 'show', $show + $limit ) ); ?>"
                    id="opiner-me-load-more"
                    class="opiner-me-load-more"
                    data-post-id="<?php echo esc_attr( $post_id ); ?>"
                    data-limit="<?php echo esc_attr( $limit ); ?>"
                    data-offset="<?php echo esc_attr( $show ); ?>"
                    data-total="<?php echo esc_attr( $total ); ?>"
                    aria-label="<?php echo esc_attr__( 'Load more opinions', 'opiner-me' ); ?>"
                    role="button"
                >
                    <?php esc_html_e( 'Show more', 'opiner-me' ); ?>
                </a>
            </div>
            <?php
        }

        return ob_get_clean();
    }
}
