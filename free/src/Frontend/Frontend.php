<?php

declare(strict_types=1);

namespace OpinerMe\Frontend;

defined( 'ABSPATH' ) || exit;

class Frontend {

    private FormHandler $form;
    private AssetsManager $assets;
    private AjaxController $ajax;

    public function __construct(
        FormHandler $form,
        AssetsManager $assets,
        AjaxController $ajax
    ) {
        $this->form   = $form;
        $this->assets = $assets;
        $this->ajax   = $ajax;
    }

    public function register(): void {
        $this->form->register();
        $this->assets->register();
        $this->ajax->register();
    }
}
