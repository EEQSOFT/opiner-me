<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<input type="number" name="opiner_me_options[opinions_per_page]" value="<?php echo esc_attr( $options['opinions_per_page'] ?? 10 ); ?>" min="1" max="1000" />
