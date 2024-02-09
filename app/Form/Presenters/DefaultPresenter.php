<?php

namespace App\Form\Presenters;

class DefaultPresenter extends Presenter
{

    /**
     * @param mixed $value
     */
    public function present($value): string
    {
        return ((string) $value) ?: '';
    }
}
