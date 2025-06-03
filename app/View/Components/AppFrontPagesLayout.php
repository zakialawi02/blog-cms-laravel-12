<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppFrontPagesLayout extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.app-front-pages');
    }
}
