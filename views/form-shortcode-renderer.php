<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<form method="post" class="opiner-me-form">
    <?php wp_nonce_field( 'opiner_me_add_opinion', 'om_nonce' ); ?>

    <input type="hidden" name="om_post_id" value="<?php echo esc_attr( intval( $post->ID ) ); ?>" />

    <p>
        <label><?php esc_html_e( 'Your name:', 'opiner-me' ); ?><br />
        <input type="text" name="om_author" value="<?php echo esc_attr( wp_unslash( $prefill['om_author'] ) ); ?>" required /></label>
    </p>

    <p>
        <label><?php esc_html_e( 'Your rating:', 'opiner-me' ); ?><br />
        <select name="om_rating" required>
            <option value="0">&nbsp;</option>

            <?php
            $opiner_me_saved_rating = intval( $prefill['om_rating'] );

            for ( $opiner_me_i = 1; $opiner_me_i <= 5; $opiner_me_i++ ) {
                printf(
                    '<option value="%d" %s>%s (%d)</option>',
                    esc_html( $opiner_me_i ),
                    selected( $opiner_me_saved_rating, $opiner_me_i, false ),
                    esc_html( str_repeat( '⭐', $opiner_me_i ) ),
                    esc_html( $opiner_me_i )
                );
            }
            ?>
        </select></label>
    </p>

    <p>
        <label><?php esc_html_e( 'Your opinion:', 'opiner-me' ); ?><br />
        <textarea name="om_content" required><?php echo esc_textarea( wp_unslash( $prefill['om_content'] ) ); ?></textarea></label>
    </p>

    <p><button type="submit" name="om_submit"><?php esc_html_e( 'Submit', 'opiner-me' ); ?></button></p>
</form>
