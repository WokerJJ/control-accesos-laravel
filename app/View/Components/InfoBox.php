<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InfoBox extends Component
{
    public $icon;
    public $color;
    public $label;
    public $value;

    public function __construct($icon, $color, $label, $value)
    {
        $this->icon  = $icon;
        $this->color = $color;
        $this->label = $label;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.info-box');
    }
}
