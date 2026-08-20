<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Diagnostics\Logger;
use OpinerMe\Utils\RequestHelper;

class OpinionSanitizer {

    private array $raw;
    private ?Logger $logger;

    public function __construct( array $raw, ?Logger $logger = null ) {
        $this->raw    = $raw;
        $this->logger = $logger;
    }

    public function sanitize(): array {
        $post_id = intval( $this->raw['om_post_id'] ?? 0 );

        $author_raw  = wp_unslash( $this->raw['om_author'] ?? '' );
        $content_raw = wp_unslash( $this->raw['om_content'] ?? '' );

        $author  = sanitize_text_field( $author_raw );
        $author  = $this->removeEmojiWithLogging( $author );

        $content = sanitize_textarea_field( $content_raw );
        $content = $this->removeEmojiWithLogging( $content );

        $rating  = intval( $this->raw['om_rating'] ?? 0 );

        $ip      = RequestHelper::get_user_ip();
        $date    = current_time( 'mysql' );

        return array(
            'post_id' => $post_id,
            'author'  => $author,
            'content' => $content,
            'rating'  => $rating,
            'ip'      => $ip,
            'date'    => $date
        );
    }

    public function removeEmojiWithLogging( string $text ): string {
        $emojiPattern = '/[\x{1F600}-\x{1F64F}'
                      . '\x{1F300}-\x{1F5FF}'
                      . '\x{1F680}-\x{1F6FF}'
                      . '\x{1F700}-\x{1F77F}'
                      . '\x{1F780}-\x{1F7FF}'
                      . '\x{1F800}-\x{1F8FF}'
                      . '\x{1F900}-\x{1F9FF}'
                      . '\x{1FA00}-\x{1FA6F}'
                      . '\x{1FA70}-\x{1FAFF}'
                      . '\x{2600}-\x{26FF}'
                      . '\x{2700}-\x{27BF}]+/u';

        preg_match_all( $emojiPattern, $text, $matches );

        if ( ! empty( $matches[0] ) ) {
            $this->logger?->warning( 'Removed emoji: ' . implode( ' ', $matches[0] ) );
        }

        return preg_replace( $emojiPattern, '', $text );
    }
}
