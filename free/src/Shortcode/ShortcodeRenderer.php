<?php

declare(strict_types=1);

namespace OpinerMe\Shortcode;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\ShortcodeHandlerInterface;

class ShortcodeRenderer {

    private ShortcodeHandlerInterface $rating_renderer;
    private ShortcodeHandlerInterface $form_renderer;
    private ShortcodeHandlerInterface $list_renderer;
    private ShortcodeHandlerInterface $schema_renderer;
    private ShortcodeHandlerInterface $all_renderer;

    public function __construct(
        ShortcodeHandlerInterface $rating_renderer,
        ShortcodeHandlerInterface $form_renderer,
        ShortcodeHandlerInterface $list_renderer,
        ShortcodeHandlerInterface $schema_renderer,
        ShortcodeHandlerInterface $all_renderer
    ) {
        $this->rating_renderer = $rating_renderer;
        $this->form_renderer   = $form_renderer;
        $this->list_renderer   = $list_renderer;
        $this->schema_renderer = $schema_renderer;
        $this->all_renderer    = $all_renderer;
    }

    public function register(): void {
        add_shortcode( 'opiner_me_rating', fn( $atts ) => $this->rating_renderer->render( (array) $atts ) );
        add_shortcode( 'opiner_me_form', fn( $atts )   => $this->form_renderer->render( (array) $atts ) );
        add_shortcode( 'opiner_me_list', fn( $atts )   => $this->list_renderer->render( (array) $atts ) );
        add_shortcode( 'opiner_me_schema', fn( $atts ) => $this->schema_renderer->render( (array) $atts ) );
        add_shortcode( 'opiner_me', fn( $atts )        => $this->all_renderer->render( (array) $atts ) );
    }
}
