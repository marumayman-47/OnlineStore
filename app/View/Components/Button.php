<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $url;
    public $type;
    public $size;
    public $icon;
    public $label;

    /**
     * Create a new component instance.
     */
    public function __construct($url = '#', $type = 'primary', $size = 'md', $icon = null, $label = 'Button')
    {
        $this->url = $url;
        $this->type = $type;
        $this->size = $size;
        $this->icon = $icon;
        $this->label = $label;
    }

    /**
     * Get the view that represents the component.
     */
    public function render()
    {
        return view('components.button');
    }
}
