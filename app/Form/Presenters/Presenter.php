<?php

namespace App\Form\Presenters;

abstract class Presenter
{
    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     */
    abstract public function present($value): string;
}
