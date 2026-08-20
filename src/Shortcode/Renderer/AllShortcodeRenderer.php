<?php

declare(strict_types=1);

namespace OpinerMe\Shortcode\Renderer;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\LoggerInterface;
use OpinerMe\Contracts\ShortcodeHandlerInterface;

class AllShortcodeRenderer implements ShortcodeHandlerInterface {

    private RatingShortcodeRenderer $rating_renderer;
    private FormShortcodeRenderer $form_renderer;
    private ListShortcodeRenderer $list_renderer;
    private SchemaShortcodeRenderer $schema_renderer;
    private LoggerInterface $logger;

    public function __construct(
        RatingShortcodeRenderer $rating_renderer,
        FormShortcodeRenderer $form_renderer,
        ListShortcodeRenderer $list_renderer,
        SchemaShortcodeRenderer $schema_renderer,
        LoggerInterface $logger
    ) {
        $this->rating_renderer = $rating_renderer;
        $this->form_renderer   = $form_renderer;
        $this->list_renderer   = $list_renderer;
        $this->schema_renderer = $schema_renderer;
        $this->logger          = $logger;
    }

    public function render( array $atts = array() ): string {
        if (! is_singular()) {
            return '';
        }

        global $post;

        $atts = shortcode_atts( array(
            'post_id' => $post->ID ?? 0,
            'theme'   => 'default',
        ), $atts, 'opiner_me' );

        $post_id = intval( $atts['post_id'] );

        if ( $post_id < 1 ) {
            $this->logger->warning( 'All shortcode called without valid post ID.' );

            return '<div class="opiner-me-error">' . esc_html__( 'Invalid post ID.', 'opiner-me' ) . '</div>';
        }

        $rating = $this->rating_renderer->render( array( 'post_id' => $post_id, 'theme' => $atts['theme'] ) );
        $form   = $this->form_renderer->render( array( 'post_id'   => $post_id, 'theme' => $atts['theme'] ) );
        $list   = $this->list_renderer->render( array( 'post_id'   => $post_id, 'theme' => $atts['theme'] ) );
        $schema = $this->schema_renderer->render( array( 'post_id' => $post_id, 'theme' => $atts['theme'] ) );

        return sprintf( '%s%s%s%s', $rating, $form, $list, $schema );
    }
}
