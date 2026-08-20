<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Diagnostics\Logger;
use wpdb;

class OpinionRepository {

    private wpdb $wpdb;
    private string $table;
    private ?Logger $logger;

    public function __construct( wpdb $wpdb, ?Logger $logger = null ) {
        $this->wpdb   = $wpdb;
        $this->table  = $wpdb->prefix . 'om_opinions';
        $this->logger = $logger;
    }

    public function save( array $data ): int {
        $inserted = $this->wpdb->insert(
            $this->table,

            array(
                'post_id'         => intval( $data['post_id'] ?? 0 ),
                'opinion_active'  => intval( $data['active'] ?? 0 ),
                'opinion_author'  => sanitize_text_field( $data['author'] ?? '' ),
                'opinion_content' => sanitize_textarea_field( $data['content'] ?? '' ),
                'opinion_rating'  => intval( $data['rating'] ?? 0 ),
                'opinion_ip'      => sanitize_text_field( $data['ip'] ?? '' ),
                'opinion_date'    => $data['date'] ?? current_time( 'mysql' ),
            ),

            array( '%d', '%d', '%s', '%s', '%d', '%s', '%s' )
        );

        if ( empty( $inserted ) ) {
            $this->logger?->error( sprintf(
                'Insert failed for post_id %d: %s | Query: %s',
                $data['post_id'] ?? 0,
                $this->wpdb->last_error,
                $this->wpdb->last_query
            ) );
        }

        return intval( $this->wpdb->insert_id );
    }
}
