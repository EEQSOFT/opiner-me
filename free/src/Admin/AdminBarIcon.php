<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class AdminBarIcon {

    public function register() {
        if ( is_admin() ) {
            add_action( 'admin_head', array( $this, 'add_menu_badge' ) );
        }

        add_action( 'admin_bar_menu', array( $this, 'add_bar_icon' ), 61 );
    }

    public function add_menu_badge(): void {
        global $menu;

        $pending = $this->get_pending_count();

        if ( $pending > 0 ) {
            foreach ( $menu as $key => $item ) {
                if ( isset( $item[2] ) && $item[2] === 'opiner-me' ) {
                    $menu[$key][0] .= ' <span class="update-plugins count-' . intval( $pending ) . '"><span class="plugin-count">' . intval( $pending ) . '</span></span>';

                    break;
                }
            }
        }
    }

    public function add_bar_icon( \WP_Admin_Bar $wp_admin_bar ): void {
        if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $pending = $this->get_pending_count();

        $title = '<span class="ab-icon dashicons dashicons-star-filled"></span>';

        if ( $pending > 0 ) {
            $title .= '<span class="ab-label">' . intval( $pending ) . '</span>';
        }

        $wp_admin_bar->add_node( array(
            'id'    => 'opiner-me-bar-icon',
            'title' => $title,
            'href'  => admin_url( 'admin.php?page=opiner-me-moderation' ),
            'meta'  => array()
        ) );
    }

    private function get_pending_count(): int {
        static $pending = null;

        if ( $pending === null ) {
            global $wpdb;

            $table      = $wpdb->prefix . 'om_opinions';
            $table_name = esc_sql( $table );

            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- table name safely escaped and safe direct COUNT query on custom table, no caching needed
            $pending = $wpdb->get_var( "SELECT COUNT(*) FROM `{$table_name}` WHERE opinion_active = 0" );
        }

        return intval( $pending );
    }
}
