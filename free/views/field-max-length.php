<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<input type="number" name="opiner_me_options[max_length]" value="<?php echo esc_attr( $options['max_length'] ?? 1000 ); ?>" min="10" max="1000" />
