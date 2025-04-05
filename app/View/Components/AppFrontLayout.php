<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppFrontLayout extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): view
    {
        return view('layouts.app-front');
    }
}
