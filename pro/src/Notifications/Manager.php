<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Notifications;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Pro\Views\ViewLoader;

class Manager {

    public function init(): void {
        add_action( 'opiner_me/opinion_added', array( $this, 'handle_new_opinion' ), 10, 2 );
    }

    public function handle_new_opinion( int $opinion_id, array $data ): void {
        $options = get_option( 'opiner_me_options', array() );

        if ( empty( $options['notify_enabled'] ) ) {
            return;
        }

        $admin_email = $options['notify_email'] ?? get_option( 'admin_email' );

        $subject = sprintf(
            /* translators: %s: Site name */
            __( 'New review on the website: %s', 'opiner-me' ),
            get_bloginfo( 'name' )
        );

        $message = $this->build_message( $opinion_id, $data );

        wp_mail(
            $admin_email,
            $subject,
            $message,
            array( 'Content-Type: text/html; charset=UTF-8' )
        );
    }

    private function build_message( int $opinion_id, array $data ): string {
        $rating  = $data['rating'] ?? '-';
        $author  = $data['author'] ?? __( 'Anonym', 'opiner-me' );
        $content = $data['content'] ?? '';

        ob_start();

        ViewLoader::load( 'email-review-added', compact( 'rating', 'author', 'content' ) );

        return ob_get_clean();
    }
}
