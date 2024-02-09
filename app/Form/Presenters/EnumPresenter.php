<?php

namespace App\Form\Presenters;

class EnumPresenter extends Presenter
{

    /**
     * @param array<int, string> $value
     */
    public function present($value): string
    {
        return is_array($value)
            ? implode(', ', $value)
            : '';
    }
}
