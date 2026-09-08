<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * @param string|null $title Page title / og:title. Falls back to the app name.
     * @param string|null $description Meta/og description for this page.
     * @param string|null $ogImage Absolute URL to use for og:image, if any.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $ogImage = null,
    ) {
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
