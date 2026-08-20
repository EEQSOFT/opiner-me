<?php

declare(strict_types=1);

namespace OpinerMe\Core;

defined( 'ABSPATH' ) || exit;

class Config {

    public const VERSION                = '1.2.1';
    public const ASSETS_VERSION         = self::VERSION;
    public const OPTION_DB_VERSION      = 'opiner_me_db_version';
    public const LICENSE_API            = 'https://api.opiner.me/api/license-api.php';
    public const LICENSE_API_DEACTIVATE = 'https://api.opiner.me/api/license-deactivate.php';
    public const LOG_SPAM_GUARD         = true;
    public const LOG_VALIDATION         = true;
}
