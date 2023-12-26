<?php

namespace App\Form\Fields;

class CheckboxesField extends Field
{
    public static function name(): string
    {
        return 'Checkboxes';
    }

    public static function meta(): array
    {
        return [
            'options' => [],
        ];
    }

    public static function default()
    {
        return [];
    }
}
