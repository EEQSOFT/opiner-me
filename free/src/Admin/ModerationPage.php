<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Service\RatingService;

class ModerationPage {

    private string $table;
    private string $table_name;

    public function __construct() {
        add_action( 'admin_init', array( $this, 'handle_actions' ) );

        global $wpdb;

        $this->table      = $wpdb->prefix . 'om_opinions';
        $this->table_name = esc_sql( $this->table );
    }

    public function render(): void {
        $notice = get_transient( 'opiner_me_notice' );

        if ( $notice ) {
            delete_transient( 'opiner_me_notice' );

            switch ( $notice ) {
                case 'activated':
                    $this->render_notice( __( 'Review activated.', 'opiner-me' ) );

                    break;
                case 'deactivated':
                    $this->render_notice( __( 'Review deactivated.', 'opiner-me' ) );

                    break;
                case 'deleted':
                    $this->render_notice( __( 'Review deleted.', 'opiner-me' ) );

                    break;
            }
        }

        $pagination = $this->get_pagination_data();
        $opinions   = $this->get_opinions( $pagination['per_page'], $pagination['offset'] );

        echo '<div class="wrap opiner-me-admin-container">';
        echo '<h1>' . esc_html__( 'Review Moderation', 'opiner-me' ) . '</h1>';

        $this->render_pagination( $pagination );
        $this->render_table( $opinions, $pagination );
        $this->render_pagination( $pagination, 'bottom' );

        echo '</div>';
    }

    public function handle_actions(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $wpdb;

        if (
            isset( $_GET['activate'], $_GET['_wpnonce'] ) &&
            wp_verify_nonce(
                sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ),
                'opiner_me_activate_' . sanitize_text_field( wp_unslash( $_GET['activate'] ) )
            )
        ) {
            $id   = intval( wp_unslash( $_GET['activate'] ) );
            $post = intval( wp_unslash( $_GET['post'] ?? 0 ) );

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Safe direct update to custom table, no caching needed
            $updated = $wpdb->update( $this->table, array( 'opinion_active' => 1 ), array( 'opinion_id' => $id ), array( '%d' ), array( '%d' ) );

            if ( ! empty( $updated ) ) {
                RatingService::update( $post );

                set_transient( 'opiner_me_notice', 'activated', 30 );

                $paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;

                wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-moderation&paged=' . $paged ) );

                exit;
            }
        }

        if (
            isset( $_GET['deactivate'], $_GET['_wpnonce'] ) &&
            wp_verify_nonce(
                sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ),
                'opiner_me_deactivate_' . sanitize_text_field( wp_unslash( $_GET['deactivate'] ) )
            )
        ) {
            $id   = intval( wp_unslash( $_GET['deactivate'] ) );
            $post = intval( wp_unslash( $_GET['post'] ?? 0 ) );

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Safe direct update to custom table, no caching needed
            $updated = $wpdb->update( $this->table, array( 'opinion_active' => 0 ), array( 'opinion_id' => $id ), array( '%d' ), array( '%d' ) );

            if ( ! empty( $updated ) ) {
                RatingService::update( $post );

                set_transient( 'opiner_me_notice', 'deactivated', 30 );

                $paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;

                wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-moderation&paged=' . $paged ) );

                exit;
            }
        }

        if (
            isset( $_GET['delete'], $_GET['_wpnonce'] ) &&
            wp_verify_nonce(
                sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ),
                'opiner_me_delete_' . sanitize_text_field( wp_unslash( $_GET['delete'] ) )
            )
        ) {
            $id   = intval( wp_unslash( $_GET['delete'] ) );
            $post = intval( wp_unslash( $_GET['post'] ?? 0 ) );

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Safe direct delete from custom table, no caching needed
            $deleted = $wpdb->delete( $this->table, array( 'opinion_id' => $id ), array( '%d' ) );

            if ( ! empty( $deleted ) ) {
                RatingService::update( $post );

                set_transient( 'opiner_me_notice', 'deleted', 30 );

                $paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;

                wp_safe_redirect( admin_url( 'admin.php?page=opiner-me-moderation&paged=' . $paged ) );

                exit;
            }
        }
    }

    private function render_notice( string $message ): void {
        echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
    }

    private function get_pagination_data(): array {
        global $wpdb;

        $per_page = 20;

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- pagination does not modify data
        $paged    = max( 1, intval( wp_unslash( $_GET['paged'] ?? 1 ) ) );
        $offset   = ( $paged - 1 ) * $per_page;

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name safely escaped and safe direct COUNT query on custom table, no caching needed
        $total       = intval( $wpdb->get_var( "SELECT COUNT(*) FROM `{$this->table_name}`" ) );
        $total_pages = ceil( $total / $per_page );

        return compact( 'per_page', 'paged', 'offset', 'total', 'total_pages' );
    }

    private function get_opinions(int $limit, int $offset): array {
        global $wpdb;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name safely escaped
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- safe SELECT query on custom table, no caching needed
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `{$this->table_name}` ORDER BY opinion_date DESC LIMIT %d OFFSET %d",
                $limit,
                $offset
            )
        );
        // phpcs:enable

        return is_array( $results ) ? $results : array();
    }

    private function render_pagination( array $pagination, string $position = 'top' ): void {
        if ( $pagination['total_pages'] < 0 ) return;

        $base = remove_query_arg( array( 'activate', 'deactivate', 'delete', 'post' ) );
        $base = add_query_arg( 'paged', '%#%', $base );

        echo '<div class="tablenav ' . esc_attr( $position ) . '">';
        echo '<div class="alignleft actions bulkactions"></div>';
        echo '<div class="tablenav-pages">';

        // translators: %d is the number of submitted opinions.
        echo '<span class="displaying-num">' . sprintf( esc_html__( '%d reviews', 'opiner-me' ), esc_html( $pagination['total'] ) ) . '</span>';

        echo wp_kses_post( paginate_links( array(
            'base'      => $base,
            'format'    => '',
            'prev_text' => '«',
            'next_text' => '»',
            'total'     => $pagination['total_pages'],
            'current'   => $pagination['paged']
        ) ) );

        echo '</div></div>';
    }

    private function render_table( array $opinions, array $pagination ): void {
        echo '<table class="wp-list-table widefat opiner-me-admin-table">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__( 'ID', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Post ID', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Author', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Rating', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Content', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'IP', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Date', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Active', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Activation', 'opiner-me' ) . '</th>';
        echo '<th>' . esc_html__( 'Action', 'opiner-me' ) . '</th>';
        echo '</tr></thead><tbody>';

        if ( empty( $opinions ) ) {
            echo '<tr><td colspan="10">' . esc_html__( 'No reviews.', 'opiner-me' ) . '</td></tr>';
            echo '</tbody></table>';

            return;
        }

        foreach ( $opinions as $op ) {
            $id     = intval( $op->opinion_id );
            $post   = intval( $op->post_id );
            $active = boolval( $op->opinion_active );
            $paged  = $pagination['paged'];

            echo '<tr>';
            echo '<td>' . esc_html( $id ) . '</td>';
            echo '<td><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( $post ) . '</a></td>';
            echo '<td>' . esc_html( $op->opinion_author ) . '</td>';
            echo '<td>' . esc_html( str_repeat( '⭐', intval( $op->opinion_rating ) ) ) . ' (' . intval( $op->opinion_rating ) . ')</td>';
            echo '<td>' . nl2br( esc_html( $op->opinion_content ) ) . '</td>';
            echo '<td>' . esc_html( $op->opinion_ip ) . '</td>';
            echo '<td>' . esc_html( $op->opinion_date ) . '</td>';

            echo '<td><span class="opiner-me-admin-color-' . ( $active ? 'green' : 'red' ) . '">' . ( $active ? esc_html__( 'Yes', 'opiner-me' ) : esc_html__( 'No', 'opiner-me' ) ) . '</span></td>';

            $action = $active ? 'deactivate' : 'activate';

            echo '<td><a href="' . esc_url( add_query_arg( array(
                'page'  => 'opiner-me-moderation',
                'paged' => $paged,
                $action => $id,
                'post'  => $post,
                '_wpnonce' => wp_create_nonce( "opiner_me_{$action}_{$id}" )
            ), admin_url( 'admin.php' ) ) ) . '">' . ( $active ? esc_html__( 'Deactivate', 'opiner-me' ) : esc_html__( 'Activate', 'opiner-me' ) ) . '</a></td>';

            echo '<td>';

            if ( class_exists( '\OpinerMe\Pro\Admin\EditReview' ) ) {
                $edit_url = add_query_arg(
                    array(
                        'page'      => 'opiner-me-edit',
                        'review_id' => $id
                    ),
                    admin_url( 'admin.php' )
                );

                echo '<a href="' . esc_url( $edit_url ) . '">'
                    . esc_html__( 'Edit', 'opiner-me' )
                    . '</a> | ';
            }

            echo '<a href="' . esc_url( add_query_arg( array(
                'page'   => 'opiner-me-moderation',
                'paged'  => $paged,
                'delete' => $id,
                'post'   => $post,
                '_wpnonce' => wp_create_nonce( "opiner_me_delete_{$id}" )
            ), admin_url( 'admin.php' ) ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Delete this review?', 'opiner-me' ) ) . '\')">' . esc_html__( 'Delete', 'opiner-me' ) . '</a>';

            echo '</td></tr>';
        }

        echo '</tbody></table>';
    }
}
