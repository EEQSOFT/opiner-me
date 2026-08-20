<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<input type="checkbox" name="opiner_me_options[auto_approve]" value="1" <?php checked( 1, $options['auto_approve'] ?? 0 ); ?> />
