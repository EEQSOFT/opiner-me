<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Cleanup/Uninstaller.php';

use OpinerMe\Cleanup\Uninstaller;

Uninstaller::run();
