<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<textarea name="opiner_me_options[blocked_words]" class="opiner-me-admin-blocked-words"><?php echo esc_textarea( $options['blocked_words'] ?? '' ); ?></textarea>
<p class="description"><?php esc_html_e( 'E.g. "ugly, scam, spam"', 'opiner-me' ); ?></p>
