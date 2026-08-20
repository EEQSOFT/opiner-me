<?php

declare(strict_types=1);

namespace OpinerMe\Schema;

defined('ABSPATH') || exit;

class SchemaBuilder {

    public static function build_rating_schema( string $type, string $title, string $url, object $rating ): array {
        return array(
            '@context' => 'https://schema.org',
            '@type'    => $type,
            'name'     => $title,
            'url'      => $url,
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id'   => $url
            ),
            'aggregateRating' => array(
                '@type'       => 'AggregateRating',
                'ratingValue' => number_format( floatval( $rating->rating_average ), 1, '.', '' ),
                'reviewCount' => $rating->rating_count
            )
        );
    }

    public static function add_reviews( array $jsonld, array $opinions ): array {
        foreach ( $opinions as $op ) {
            $jsonld['review'][] = array(
                '@type'  => 'Review',
                'author' => array(
                    '@type' => 'Person',
                    'name'  => wp_unslash( $op->opinion_author ) ?: 'Anonymous'
                ),
                'datePublished' => gmdate( 'Y-m-d', strtotime( $op->opinion_date ) ),
                'reviewBody'    => wp_trim_words( wp_unslash( $op->opinion_content ), 50, '...' ),
                'reviewRating'  => array(
                    '@type'       => 'Rating',
                    'ratingValue' => $op->opinion_rating,
                    'bestRating'  => '5'
                )
            );
        }

        return $jsonld;
    }
}
