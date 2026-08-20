<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Diagnostics\Logger;
use OpinerMe\Service\RatingService;
use OpinerMe\Validation\OpinionValidator;

class FormHandler {

    private OpinionRepository $repository;
    private SpamGuard $spam;
    private RatingService $rating_service;
    private ?Logger $logger;

    public function __construct(
        OpinionRepository $repository,
        SpamGuard $spam,
        RatingService $rating_service,
        ?Logger $logger = null
    ) {
        $this->repository     = $repository;
        $this->spam           = $spam;
        $this->rating_service = $rating_service;
        $this->logger         = $logger;
    }

    public function register(): void {
        add_action( 'init', array( $this, 'handle_form' ) );
    }

    public function handle_form(): void {
        if ( ! isset( $_POST['om_submit'] ) ) {
            return;
        }

        if (
            ! isset( $_POST['om_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['om_nonce'] ) ), 'opiner_me_add_opinion' )
        ) {
            return;
        }

        $sanitizer = new OpinionSanitizer( $_POST, $this->logger );
        $data      = $sanitizer->sanitize();

        $options        = get_option( 'opiner_me_options', array() );
        $data['active'] = intval( $options['auto_approve'] ?? 0 );

        $validator = new OpinionValidator(
            $data['post_id'],
            $data['author'],
            $data['content'],
            $data['rating'],
            $options,
            $this->logger
        );

        $error = $validator->validate();

        if ( $error->has_errors() ) {
            $unique = 'opiner_me_form_' . get_current_user_id();

            set_transient( $unique, array(
                'errors' => $error,

                'fields' => array(
                    'om_author'  => $data['author'],
                    'om_content' => $data['content'],
                    'om_rating'  => $data['rating'],
                ),
            ), 60 );

            wp_safe_redirect( add_query_arg( 'opiner_me_msg', 'error', wp_get_referer() ) );

            exit;
        }

        $this->spam->check( $data['post_id'] );

        $saved = $this->repository->save( $data );

        if ( ! empty( $saved ) ) {
            do_action( 'opiner_me/opinion_added', $saved, $data );

            $this->rating_service->update( $data['post_id'] );

            wp_safe_redirect( add_query_arg( 'opiner_me_msg', 'success', wp_get_referer() ) );

            exit;
        }
    }
}
