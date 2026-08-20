<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Views\ViewLoader;

class FieldRenderer {

    public function render_auto_approve(): void {
        $options = get_option( 'opiner_me_options' );

        ViewLoader::load( 'field-auto-approve', compact( 'options' ) );
    }

    public function render_opinions_per_page(): void {
        $options = get_option( 'opiner_me_options' );

        ViewLoader::load( 'field-opinions-per-page', compact( 'options' ) );
    }

    public function render_min_length(): void {
        $options = get_option( 'opiner_me_options' );

        ViewLoader::load( 'field-min-length', compact( 'options' ) );
    }

    public function render_max_length(): void {
        $options = get_option( 'opiner_me_options' );

        ViewLoader::load( 'field-max-length', compact( 'options' ) );
    }

    public function render_blocked_words(): void {
        $options = get_option( 'opiner_me_options' );

        ViewLoader::load( 'field-blocked-words', compact( 'options' ) );
    }

    public function render_display_schema(): void {
        $options = get_option( 'opiner_me_options' );
        $schema = $options['display_schema'] ?? 'rating';

        ViewLoader::load( 'field-display-schema', compact( 'schema' ) );
    }
}
