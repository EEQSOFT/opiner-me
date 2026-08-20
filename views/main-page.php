<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap opiner-me-admin-container">
    <h1>Opiner Me - 📋 <?php esc_html_e( 'Plugin shortcodes', 'opiner-me' ); ?></h1>
    <p><?php esc_html_e( 'Use the shortcodes below to display review and rating modules on any page or post.', 'opiner-me' ); ?></p>

    <div class="opiner-me-admin-shortcode-box">
        <h2><?php esc_html_e( 'Rating', 'opiner-me' ); ?></h2>
        <p><?php esc_html_e( 'This shortcode displays the average review rating, number of votes, and stars.', 'opiner-me' ); ?></p>
        <code>[opiner_me_rating]</code>
    </div>

    <div class="opiner-me-admin-shortcode-box">
        <h2><?php esc_html_e( 'Form', 'opiner-me' ); ?></h2>
        <p><?php esc_html_e( 'This shortcode displays a form for adding reviews.', 'opiner-me' ); ?></p>
        <code>[opiner_me_form]</code>
    </div>

    <div class="opiner-me-admin-shortcode-box">
        <h2><?php esc_html_e( 'List', 'opiner-me' ); ?></h2>
        <p><?php esc_html_e( 'This shortcode displays a list of reviews added via a form.', 'opiner-me' ); ?></p>
        <code>[opiner_me_list]</code>
    </div>

    <div class="opiner-me-admin-shortcode-box">
        <h2><?php esc_html_e( 'Schema', 'opiner-me' ); ?></h2>
        <p><?php esc_html_e( 'This shortcode adds JSON-LD Schema.', 'opiner-me' ); ?></p>
        <code>[opiner_me_schema]</code>
    </div>

    <div class="opiner-me-admin-shortcode-box">
        <h2><?php esc_html_e( 'All in One', 'opiner-me' ); ?></h2>
        <p><?php esc_html_e( 'Instead of adding all of the above, you can add just one shortcode to your post or page.', 'opiner-me' ); ?></p>
        <code>[opiner_me]</code>
    </div>

    <div class="opiner-me-admin-shortcode-box">
        <h2><?php esc_html_e( 'Custom Parameters', 'opiner-me' ); ?></h2>
        <p><?php esc_html_e( 'E.g. for the "opiner_me_rating" shortcode you can provide "post_id", "show_count", and "show_stars".', 'opiner-me' ); ?></p>
        <code>[opiner_me_rating post_id="19" show_count="true" show_stars="true"]</code>
    </div>

    <div class="opiner-me-admin-green-button">
        <a href="https://opiner.me" target="_blank"><?php esc_html_e( 'Visit the Opiner.Me website', 'opiner-me' ); ?></a>
    </div>

    <div class="opiner-me-admin-coffee-button">
        <a href="https://www.paypal.me/WEBEEQ" target="_blank">☕ <?php esc_html_e( 'Donate for Coffee', 'opiner-me' ); ?></a>
    </div>
</div>
