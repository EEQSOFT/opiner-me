<?php

declare(strict_types=1);

namespace OpinerMe\Setup;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Core\{ Config, Logger };
use OpinerMe\DB\Installer;

class Activator {

    public static function activate(): void {
        try {
            Installer::activate();
        } catch ( \Throwable $e ) {
            Logger::error( sprintf(
                'Activation error: %s in %s:%d',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ) );
        }

        update_option( Config::OPTION_DB_VERSION, Config::VERSION );
    }
}
