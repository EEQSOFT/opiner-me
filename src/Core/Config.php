<?php

declare(strict_types=1);

namespace OpinerMe\Core;

defined( 'ABSPATH' ) || exit;

class Config {
    public const VERSION           = '1.1.0';
    public const ASSETS_VERSION    = self::VERSION;
    public const OPTION_DB_VERSION = 'opiner_me_db_version';
    public const LOG_SPAM_GUARD    = true;
    public const LOG_VALIDATION    = true;
}
