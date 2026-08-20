<?php

declare(strict_types=1);

namespace OpinerMe\DB;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Diagnostics\Logger;

class DataMigrator {

    public static function migrate( string $new_data, string $old_data ): void {
        $old = get_option( $old_data );

        if ( ! empty( $old ) ) {
            update_option( $new_data, $old );
            delete_option( $old_data );

            Logger::info( 'Data migration completed.' );
        }
    }
}
