<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<input type="number" name="opiner_me_options[min_length]" value="<?php echo esc_attr( $options['min_length'] ?? 10 ); ?>" min="10" max="1000" />
