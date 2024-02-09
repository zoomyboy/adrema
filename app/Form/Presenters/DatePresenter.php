<?php

namespace App\Form\Presenters;

use Carbon\Carbon;

class DatePresenter extends Presenter
{

    public function present($value): string
    {
        return $value ? Carbon::parse($value)->format('d.m.Y') : '';

    }

}
