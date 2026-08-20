<?php
/**
 * Plugin Name: Opiner Me – Lightweight Star Rating & Review Form
 * Plugin URI: https://opiner.me
 * Description: Simple star rating & opinions plugin with frontend form, admin panel, and JSON-LD Schema.
 * Author: Opiner Me
 * Author URI: https://opiner.me
 * Version: 1.2.2
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * Text Domain: opiner-me
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'OPINER_ME_URL', plugin_dir_url( __FILE__ ) );
define( 'OPINER_ME_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPINER_ME_FILE', __FILE__ );
define( 'OPINER_ME_DIR', __DIR__ );

require_once __DIR__ . '/free/Loader.php';

$opiner_me_free_loader = new \OpinerMe\Free\Loader();
$opiner_me_free_loader->init();

$opiner_me_license_manager = new \OpinerMe\Admin\LicenseManager;

if ( $opiner_me_license_manager::is_pro_active() ) {
    require_once __DIR__ . '/pro/Loader.php';

    $opiner_me_pro_loader = new \OpinerMe\Pro\Loader();
    $opiner_me_pro_loader->init();
}
