<?php

declare(strict_types=1);

namespace OpinerMe\Pro\Plugin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Pro\Admin\{ Editor, EditReview, SettingsPro, SettingsProValidator };
use OpinerMe\Pro\Helpers\Utils;
use OpinerMe\Pro\ImportExport\{ Exporter, Importer };
use OpinerMe\Pro\Notifications\Manager as NotificationsManager;
use OpinerMe\Pro\Widgets\Slider\{ SliderAssets, SliderWidget };

class Plugin {

    public function __construct() {
        $this->init_modules();
        $this->init_settings();
    }

    private function init_modules(): void {
        $slider_widget = new SliderWidget();
        // $editor = new Editor();
        $edit_review = new EditReview();
        $notifications_manager = new NotificationsManager();
        $exporter = new Exporter();
        $importer = new Importer();

        SliderAssets::init();

        $slider_widget->init();
        // $editor->init();
        $edit_review->init();
        $notifications_manager->init();
        $exporter->init();
        $importer->init();

        Utils::init();
    }

    private function init_settings(): void {
        new SettingsPro();
        new SettingsProValidator();
    }
}
