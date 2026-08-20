<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<select name="opiner_me_options[display_schema]">
    <option value="none" <?php selected( $schema, 'none' ); ?>><?php esc_html_e( 'No JSON-LD Schema', 'opiner-me' ); ?></option>
    <option value="rating" <?php selected( $schema, 'rating' ); ?>><?php esc_html_e( 'Average rating only', 'opiner-me' ); ?></option>
    <option value="list" <?php selected( $schema, 'list' ); ?>><?php esc_html_e( 'Rating with opinions list', 'opiner-me' ); ?></option>
</select>
