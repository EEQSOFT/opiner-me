<?php

declare(strict_types=1);

namespace OpinerMe\Validation;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\Config;
use OpinerMe\Diagnostics\Logger;
use OpinerMe\Frontend\OpinionSanitizer;
use OpinerMe\Utils\RequestHelper;

class OpinionValidator {

    private int $post_id;
    private string $author;
    private string $content;
    private int $rating;
    private array $options;
    private ?Logger $logger;

    public function __construct(
        int $post_id,
        string $author,
        string $content,
        int $rating,
        array $options,
        ?Logger $logger = null
    ) {
        $this->post_id = $post_id;
        $this->author  = $author;
        $this->content = $content;
        $this->rating  = $rating;
        $this->options = $options;
        $this->logger  = $logger;
    }

    public function validate(): \WP_Error {
        $error = new \WP_Error();

        $min_length = intval( $this->options['min_length'] ?? 10 );
        $max_length = intval( $this->options['max_length'] ?? 1000 );
        $blocked    = explode( ',', strval( $this->options['blocked_words'] ?? '' ) );

        $ip  = RequestHelper::get_user_ip();
        $now = current_time( 'mysql' );

        $messages = array();

        if ( $this->post_id < 1 || ! get_post_status( $this->post_id ) ) {
            $messages[] = 'Invalid post ID.';

            $error->add( 'invalid_post', esc_html__( 'Invalid post ID.', 'opiner-me' ) );
        }

        if ( ! preg_match( '/^[\p{L}\p{N}\s\-]+$/u', $this->author ) ) {
            $messages[] = 'Name contains invalid characters.';

            $error->add( 'invalid_author_chars', esc_html__( 'Name contains invalid characters.', 'opiner-me' ) );
        }

        if ( strlen( $this->author ) < 1 || strlen( $this->author ) > 50 ) {
            $messages[] = 'Enter your name (1-50).';

            $error->add( 'invalid_author', esc_html__( 'Enter your name (1-50).', 'opiner-me' ) );
        }

        if ( $this->rating < 1 || $this->rating > 5 ) {
            $messages[] = 'Select your rating (1-5).';

            $error->add( 'invalid_rating', esc_html__( 'Select your rating (1-5).', 'opiner-me' ) );
        }

        if ( strlen( $this->content ) < $min_length || strlen( $this->content ) > $max_length ) {
            $messages[] = sprintf( 'Enter your opinion (%d-%d).', $min_length, $max_length );

            // translators: %1$d is the minimum length, %2$d is the maximum length of the opinion.
            $error->add( 'invalid_content', sprintf( esc_html__( 'Enter your opinion (%1$d-%2$d).', 'opiner-me' ), $min_length, $max_length ) );
        }

        if ( ! empty( $blocked ) ) {
            $found = array();

            foreach ( $blocked as $word ) {
                $word = trim( $word );

                if ( $word !== '' && preg_match( '/\b' . preg_quote( $word, '/' ) . '\b/i', $this->content ) ) {
                    $found[] = $word;
                }
            }

            if ( ! empty( $found ) ) {
                $messages[] = 'Banned words: ' . implode( ', ', $found );

                $error->add( 'invalid_words', esc_html__( 'Banned words found:', 'opiner-me' ) . ' ' . implode( ', ', $found ) );
            }
        }

        if ( Config::LOG_VALIDATION && ! empty( $messages ) ) {
            $this->logger?->warning( sprintf(
                'Validation issues for post_id %d, ip %s, date %s: %s',
                $this->post_id,
                $ip,
                $now,
                implode( ' | ', $messages )
            ) );
        }

        return $error;
    }
}
