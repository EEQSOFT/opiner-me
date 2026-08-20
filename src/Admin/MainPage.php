<?php

declare(strict_types=1);

namespace OpinerMe\Admin;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Views\ViewLoader;

class MainPage {

    public function render(): void {
        ViewLoader::load( 'main-page' );
    }
}
