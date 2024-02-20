<?php

namespace App\Form\Presenters;

class NamiPresenter extends Presenter
{

    /**
     * @param ?array<int, array{id: int}> $value
     */
    public function present($value): string
    {
        return collect(array_column($value, 'id'))->implode(', ');
    }
}
