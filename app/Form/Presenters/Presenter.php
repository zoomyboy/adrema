<?php

namespace App\Form\Presenters;

abstract class Presenter
{
    /* @var mixed */
    public $value;

    abstract public function present($value): string;
}
