<?php

declare(strict_types=1);

namespace OpinerMe\Plugin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Admin\{ AdminBarIcon, AssetsManager, LicenseManager, MenuManager, SettingsPanel };
use OpinerMe\DB\SchemaManager;
use OpinerMe\Diagnostics\{ Logger, LogViewer };
use OpinerMe\Frontend\{
    AjaxController,
    AssetsManager as PublicAssetsManager,
    FormHandler,
    Frontend,
    OpinionRepository,
    SpamGuard
};
use OpinerMe\Renderer\RatingRenderer;
use OpinerMe\Service\RatingService;
use OpinerMe\Setup\{ Activator, Deactivator };
use OpinerMe\Shortcode\Renderer\{
    AllShortcodeRenderer,
    FormShortcodeRenderer,
    ListShortcodeRenderer,
    RatingShortcodeRenderer,
    SchemaShortcodeRenderer
};
use OpinerMe\Shortcode\ShortcodeRenderer;

class Plugin {

    public function __construct() {
        $this->load_dependencies();
        $this->setup_logging();
        $this->setup_database();
        $this->setup_admin();
        $this->setup_admin_bar();
        $this->setup_frontend();
        $this->setup_shortcode_renderer();
    }

    private function load_dependencies(): void {
        register_activation_hook( OPINER_ME_FILE, array( Activator::class, 'activate' ) );
        register_deactivation_hook( OPINER_ME_FILE, array( Plugin::class, 'deactivate_all' ) );
    }

    public static function deactivate_all(): void {
        Deactivator::deactivate();
        LicenseManager::deactivate_plugin();
    }

    private function setup_logging(): void {
        // Logger::info( 'Plugin running.' );
    }

    private function setup_database(): void {
        SchemaManager::maybe_update_schema();
        // DataMigrator::migrate( 'opiner_me_new_data', 'opiner_me_old_data' );
    }

    private function setup_admin(): void {
        if ( is_admin() ) {
            $settings_panel = new SettingsPanel();
            $settings_panel->register();

            $menu_manager = new MenuManager();
            $menu_manager->register();

            $assets_manager = new AssetsManager();
            $assets_manager->register();

            LogViewer::register();
        }
    }

    private function setup_admin_bar(): void {
        $admin_bar = new AdminBarIcon();

        $admin_bar->register();
    }

    private function setup_frontend(): void {
        global $wpdb;

        $logger         = Logger::getInstance();
        $rating_service = new RatingService();
        $repository     = new OpinionRepository( $wpdb, $logger );
        $spam_guard     = new SpamGuard( $logger );

        $form_handler = new FormHandler( $repository, $spam_guard, $rating_service, $logger );
        $assets       = new PublicAssetsManager();
        $ajax         = new AjaxController();

        $frontend = new Frontend(
            $form_handler,
            $assets,
            $ajax
        );

        $frontend->register();
    }

    private function setup_shortcode_renderer(): void {
        $logger          = Logger::getInstance();
        $rating_service  = new RatingService();
        $rating_renderer = new RatingRenderer();

        $rating_shortcode_renderer = new RatingShortcodeRenderer( $rating_service, $rating_renderer, $logger );
        $form_shortcode_renderer   = new FormShortcodeRenderer( $logger );
        $list_shortcode_renderer   = new ListShortcodeRenderer( $logger );
        $schema_shortcode_renderer = new SchemaShortcodeRenderer( $logger );
        $all_shortcode_renderer    = new AllShortcodeRenderer(
            $rating_shortcode_renderer,
            $form_shortcode_renderer,
            $list_shortcode_renderer,
            $schema_shortcode_renderer,
            $logger
        );

        $shortcode_renderer = new ShortcodeRenderer(
            $rating_shortcode_renderer,
            $form_shortcode_renderer,
            $list_shortcode_renderer,
            $schema_shortcode_renderer,
            $all_shortcode_renderer
        );

        $shortcode_renderer->register();
    }
}
