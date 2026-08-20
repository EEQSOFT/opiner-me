<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Service\RatingService;

class EditReview {

    public function init(): void {
        add_action( 'admin_menu', array( $this, 'register_page' ) );
        add_action( 'admin_post_opiner_me_update_review', array( $this, 'update_review' ) );
        add_filter( 'parent_file', array( $this, 'highlight_menu' ) );
        add_filter( 'submenu_file', array( $this, 'highlight_submenu' ) );
        add_action( 'admin_head', array( $this, 'hide_edit_submenu' ) );
        add_filter( 'admin_title', array( $this, 'fix_admin_title' ), 10, 2 );
    }

    public function register_page(): void {
        add_submenu_page(
            'opiner-me',
            __( 'Edit Review', 'opiner-me' ),
            __( 'Edit Review', 'opiner-me' ),
            'manage_options',
            'opiner-me-edit',
            array( $this, 'render_page' )
        );

        add_submenu_page(
            'opiner-me-moderation',
            '',
            '',
            'manage_options',
            'opiner-me-edit-hidden',
            '__return_null'
        );
    }

    public function highlight_menu( ?string $parent ): ?string {
        global $plugin_page;

        if ( $plugin_page === 'opiner-me-edit' ) {
            return 'opiner-me';
        }

        return $parent;
    }

    public function highlight_submenu( ?string $submenu_file ): ?string {
        global $plugin_page;

        if ( $plugin_page === 'opiner-me-edit' ) {
            return 'opiner-me-moderation';
        }

        return $submenu_file;
    }

    public function hide_edit_submenu(): void {
        echo '<style>
            a[href="admin.php?page=opiner-me-edit"] {
                display: none !important;
            }
        </style>';
    }

    public function fix_admin_title( string $admin_title, string $title ): string {
        global $plugin_page;

        if ( $plugin_page === 'opiner-me-edit' ) {
            return __( 'Edit Review', 'opiner-me' );
        }

        return $admin_title;
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'No permission' );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $review_id = isset( $_GET['review_id'] ) ? intval( $_GET['review_id'] ) : 0;

        if ( $review_id <= 0 ) {
            wp_die( 'Invalid review ID' );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['updated'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>'
                . esc_html__( 'Review updated successfully.', 'opiner-me' )
                . '</p></div>';
        }

        global $wpdb;

        $table = $wpdb->prefix . 'om_opinions';

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
        $review = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM `{$table}` WHERE opinion_id = %d LIMIT 1",
            $review_id
        ) );
        // phpcs:enable

        if ( ! $review ) {
            wp_die( 'Review not found' );
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Edit Review', 'opiner-me' ) . '</h1>';

        settings_errors( 'opiner_me_edit_review' );

        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';

        wp_nonce_field( 'om_edit_update_review', 'om_edit_update_nonce' );

        echo '<input type="hidden" name="action" value="opiner_me_update_review" />';
        echo '<input type="hidden" name="om_edit_review_id" value="' . intval( $review_id ) . '" />';

        echo '<table class="form-table">';

        echo '<tr><th>' . esc_html__( 'Post ID', 'opiner-me' ) . '</th><td>';
        echo '<input type="number" name="om_edit_post_id" value="' . intval( $review->post_id ) . '" />';
        echo '<p class="description">' . esc_html__( 'ID of the post or page associated with the review.', 'opiner-me' ) . '</p>';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__( 'Post Slug', 'opiner-me' ) . '</th><td>';
        echo '<input type="text" name="om_edit_post_slug" value="" class="regular-text" />';
        echo '<p class="description">' . esc_html__( 'Optional: enter slug and the system will find the ID itself.', 'opiner-me' ) . '</p>';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__( 'Author', 'opiner-me' ) . '</th><td>';
        echo '<input type="text" name="om_edit_author" value="' . esc_attr( $review->opinion_author ) . '" class="regular-text" />';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__( 'Content', 'opiner-me' ) . '</th><td>';
        echo '<textarea name="om_edit_content" rows="10" class="large-text">' . esc_textarea( $review->opinion_content ) . '</textarea>';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__( 'Rating', 'opiner-me' ) . '</th><td>';
        echo '<input type="number" name="om_edit_rating" min="1" max="5" value="' . intval( $review->opinion_rating ) . '" />';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__( 'Active', 'opiner-me' ) . '</th><td>';
        echo '<label><input type="checkbox" name="om_edit_active" value="1" ' . checked( $review->opinion_active, 1, false ) . ' /> ';
        echo esc_html__( 'Visible on site', 'opiner-me' ) . '</label>';
        echo '</td></tr>';

        echo '</table>';

        submit_button( __( 'Save Changes', 'opiner-me' ) );

        echo '</form>';
        echo '</div>';
    }

    public function update_review(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'No permission' );
        }

        check_admin_referer( 'om_edit_update_review', 'om_edit_update_nonce' );

        $review_id = isset( $_POST['om_edit_review_id'] )
            ? intval( wp_unslash( $_POST['om_edit_review_id'] ) )
            : 0;

        if ( $review_id <= 0 ) {
            wp_die( 'Invalid review ID' );
        }

        $post_id = isset( $_POST['om_edit_post_id'] )
            ? intval( wp_unslash( $_POST['om_edit_post_id'] ) )
            : 0;

        global $wpdb;

        if ( $post_id === 0 && ! empty( $_POST['om_edit_post_slug'] ) ) {
            $slug = sanitize_title( wp_unslash( $_POST['om_edit_post_slug'] ) );

            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
            $post_id = intval( $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM `{$wpdb->posts}`
                    WHERE post_name = %s
                    AND post_status = 'publish'
                    AND post_type IN ('post','page')
                    ORDER BY ID DESC
                    LIMIT 1",
                    $slug
                )
            ) );
            // phpcs:enable
        }

        $author  = isset( $_POST['om_edit_author'] ) ? sanitize_text_field( wp_unslash( $_POST['om_edit_author'] ) ) : '';
        $content = isset( $_POST['om_edit_content'] ) ? wp_kses_post( wp_unslash( $_POST['om_edit_content'] ) ) : '';
        $rating  = isset( $_POST['om_edit_rating'] ) ? intval( wp_unslash( $_POST['om_edit_rating'] ) ) : 0;
        $active  = isset( $_POST['om_edit_active'] ) ? 1 : 0;

        $table = $wpdb->prefix . 'om_opinions';

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $table,

            array(
                'post_id'         => $post_id,
                'opinion_author'  => $author,
                'opinion_content' => $content,
                'opinion_rating'  => $rating,
                'opinion_active'  => $active
            ),

            array( 'opinion_id' => $review_id ),
            array( '%d', '%s', '%s', '%d', '%d' ),
            array( '%d' )
        );
        // phpcs:enable

        RatingService::update( $post_id );

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page'      => 'opiner-me-edit',
                    'review_id' => $review_id,
                    'updated'   => 'true',
                ),

                admin_url( 'admin.php' )
            )
        );

        exit;
    }
}
