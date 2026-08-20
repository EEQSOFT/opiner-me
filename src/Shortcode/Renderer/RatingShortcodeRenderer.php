<?php

declare(strict_types=1);

namespace OpinerMe\Shortcode\Renderer;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\LoggerInterface;
use OpinerMe\Contracts\ShortcodeHandlerInterface;
use OpinerMe\Renderer\RatingRenderer;
use OpinerMe\Service\RatingService;

class RatingShortcodeRenderer implements ShortcodeHandlerInterface {

    private RatingService $rating_service;
    private RatingRenderer $rating_renderer;
    private LoggerInterface $logger;

    public function __construct(
        RatingService $rating_service,
        RatingRenderer $rating_renderer,
        LoggerInterface $logger
    ) {
        $this->rating_service  = $rating_service;
        $this->rating_renderer = $rating_renderer;
        $this->logger          = $logger;
    }

    public function render( array $atts = array() ): string {
        if ( ! is_singular() ) {
            return '';
        }

        global $post;

        $atts = shortcode_atts( array(
            'post_id'    => $post->ID ?? 0,
            'show_count' => true,
            'show_stars' => true,
            'theme'      => 'stars'
        ), $atts, 'opiner_me_rating' );

        $post_id = intval( $atts['post_id'] );

        if ( $post_id < 1 ) {
            $this->logger->warning( 'Rating shortcode called without valid post ID.' );

            return '<div class="opiner-me-error">' . esc_html__( 'Invalid post ID.', 'opiner-me' ) . '</div>';
        }

        $show_count = filter_var( $atts['show_count'], FILTER_VALIDATE_BOOLEAN );
        $show_stars = filter_var( $atts['show_stars'], FILTER_VALIDATE_BOOLEAN );

        $data = $this->rating_service->get( $post_id );

        $output = '<div class="opiner-me-rating">';

        if ( $show_stars ) {
            $output .= $this->rating_renderer->render_stars( $data['average'] );
        }

        $output .= '<div class="opiner-me-rating-text">';
        $output .= esc_html__( 'Average rating:', 'opiner-me' ) . ' ' . floatval( $data['average'] ) . ' / 5';

        if ( $show_count ) {
            $output .= ' (' . intval( $data['count'] ) . ' ' . esc_html__( 'votes', 'opiner-me' ) . ')';
        }

        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}
