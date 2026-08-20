<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<form method="post" action="<?php echo esc_url( $url ); ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="opiner_me_import_reviews" />
    <?php wp_nonce_field( 'opiner_me_import_reviews', 'opiner_me_import_nonce' ); ?>
    <input type="file" name="opiner_me_import" accept=".json" required />
    <?php submit_button( __( 'Import reviews', 'opiner-me' ) ); ?>
</form>
