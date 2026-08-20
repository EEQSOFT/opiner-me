<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<input type="text" name="opiner_me_options[blocked_words]" value="<?php echo esc_attr( $options['blocked_words'] ?? '' ); ?>" class="opiner-me-admin-blocked-words" />
<p class="description"><?php esc_html_e( 'E.g. "ugly, scam, spam"', 'opiner-me' ); ?></p>
