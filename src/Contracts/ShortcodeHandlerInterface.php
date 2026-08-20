<?php

declare(strict_types=1);

namespace OpinerMe\Contracts;

defined( 'ABSPATH' ) || exit;

interface ShortcodeHandlerInterface {

    public function render(): string;
}
