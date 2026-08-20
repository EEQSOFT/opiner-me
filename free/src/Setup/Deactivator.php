<?php

declare(strict_types=1);

namespace OpinerMe\Setup;

defined( 'ABSPATH' ) || exit;

class Deactivator {

    public static function deactivate(): void {
        // wp_clear_scheduled_hook( 'opiner_me_cron' );
    }
}
