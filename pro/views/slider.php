<?php

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="opiner-me-slider-wrapper">
    <div
        class="opiner-me-slider <?php echo $fade ? 'opiner-me-fade-mode' : '' ?>"
        data-autoplay="<?php echo $autoplay ? intval( $speed ) : '' ?>"
    >
        <div class="opiner-me-slider-track">
            <?php foreach ( $opinions as $opiner_me_op ) { ?>
                <div class="opiner-me-slide">
                    <div class="opiner-me-slide-inner">
                        <div class="opiner-me-slide-rating">
                            <?php echo esc_html( str_repeat( '⭐', intval( $opiner_me_op->opinion_rating ) ) ); ?>
                        </div>

                        <div class="opiner-me-slide-content">
                            <?php echo esc_html(
                                \OpinerMe\Pro\Widgets\Slider\SliderService::shorten(
                                    $opiner_me_op->opinion_content,
                                    $max_words
                                )
                            ); ?>
                        </div>

                        <div class="opiner-me-slide-author">
                            — <?php echo esc_html( $opiner_me_op->opinion_author ); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php if ( $arrows ) { ?>
            <div class="opiner-me-slider-arrow prev">‹</div>
            <div class="opiner-me-slider-arrow next">›</div>
        <?php } ?>

        <?php if ( $dots ) { ?>
            <div class="opiner-me-slider-dots">
                <?php foreach ( $opinions as $opiner_me_i => $opiner_me_op ) { ?>
                    <span class="opiner-me-slider-dot" data-index="<?php echo intval( $opiner_me_i ); ?>"></span>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
