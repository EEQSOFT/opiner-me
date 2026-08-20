<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// require_once __DIR__ . '/free/vendor/autoload.php';
require_once __DIR__ . '/free/Loader.php';

$opiner_me_loader = new \OpinerMe\Free\Loader();
$opiner_me_loader->register_only_autoloader();

use OpinerMe\Cleanup\Uninstaller;

Uninstaller::run();
