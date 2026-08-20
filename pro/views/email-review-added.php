<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<h2><?php echo esc_html__( 'A new review has been added', 'opiner-me' ); ?></h2>

<p><strong><?php echo esc_html__( 'Author:', 'opiner-me' ); ?></strong> <?php echo esc_html( wp_unslash( $author ) ); ?></p>
<p><strong><?php echo esc_html__( 'Rating:', 'opiner-me' ); ?></strong> <?php echo esc_html( $rating ); ?>/5</p>

<p><strong><?php echo esc_html__( 'Content of the opinion:', 'opiner-me' ); ?></strong></p>
<p><?php echo nl2br( esc_html( wp_unslash( $content ) ) ); ?></p>

<p>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=opiner-me-moderation' ) ); ?>">
        <?php echo esc_html__( 'See in the panel', 'opiner-me' ); ?>
    </a>
</p>
