<?php

namespace App\Form\Fields;

class CheckboxField extends Field
{
    public static function name(): string
    {
        return 'Checkbox';
    }

    public static function meta(): array
    {
        return [
            'description' => '',
        ];
    }

    public static function default()
    {
        return false;
    }
}
