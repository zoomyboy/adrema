<?php

namespace App\Form\Presenters;

use App\Form\Fields\GroupField;
use App\Group;

class GroupPresenter extends Presenter
{

    private GroupField $field;

    public function field(GroupField $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @param ?int $value
     */
    public function present($value): string
    {
        if ($value === -1) {
            return $this->field->emptyOptionValue;
        }

        if (!$value) {
            return '';
        }

        return Group::find($value)?->display() ?: '';
    }
}
