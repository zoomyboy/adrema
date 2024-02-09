<?php

namespace App\Form\Presenters;

use App\Group;
use Carbon\Carbon;

class GroupPresenter extends Presenter
{

    /**
     * @param ?int $value
     */
    public function present($value): string
    {
        if (!$value) {
            return '';
        }

        return Group::find($value)?->display() ?: '';
    }
}
