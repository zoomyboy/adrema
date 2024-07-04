<?php

namespace App\View\Mail;

use Illuminate\View\Component;

/** @todo replace content with EditorData */
class Editor extends Component
{
    /**
     * Create a new component instance.
     *
     * @param array<int, mixed> $content
     * @return void
     */
    public function __construct(public array $content)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.mail.editor');
    }
}
