<?php

namespace App\Form\Presenters;

class BooleanPresenter extends Presenter
{

    /**
     * @param mixed $value
     */
    public function present($value): string
    {
        return $value ? 'Ja' : 'Nein';
    }
}
