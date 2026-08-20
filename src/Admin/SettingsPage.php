<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsPage {

    public function render(): void {
        echo '<div class="wrap opiner-me-admin-container">';
        echo '<h1>' . esc_html__( 'Plugin Settings', 'opiner-me' )  . '</h1>';

        settings_errors( 'opiner_me_options' );

        echo '<form method="post" action="options.php">';

        settings_fields( 'opiner_me_options_group' );
        do_settings_sections( 'opiner-me-settings' );
        submit_button();

        echo '</form>';
        echo '</div>';
    }
}
