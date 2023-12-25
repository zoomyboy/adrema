<?php

namespace App\Form\Fields;

class DropdownField extends Field
{
    public static function name(): string
    {
        return 'Dropdown';
    }

    public static function meta(): array
    {
        return [
            'options' => [],
        ];
    }

    public static function default()
    {
        return null;
    }
}
