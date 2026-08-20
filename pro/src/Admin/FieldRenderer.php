<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Pro\Views\ViewLoader;

class FieldRenderer {

    public function render_enabled_field(): void {
        $options = get_option( 'opiner_me_options', array() );
        $value = $options['notify_enabled'] ?? 0;

        ViewLoader::load( 'field-notify-enabled', compact( 'value' ) );
    }

    public function render_email_field(): void {
        $options = get_option( 'opiner_me_options', array() );
        $value = $options['notify_email'] ?? get_option( 'admin_email' );

        ViewLoader::load( 'field-notify-email', compact( 'value' ) );
    }

    public function render_import_reviews_form(): void {
        $url = admin_url( 'admin-post.php' );

        ViewLoader::load( 'form-import-reviews', compact( 'url' ) );
    }

    public function render_export_reviews_button(): void {
        $url = admin_url( 'admin-post.php?action=opiner_me_export_reviews' );

        ViewLoader::load( 'button-export-reviews', compact( 'url' ) );
    }
}
