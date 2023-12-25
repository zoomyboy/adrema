<?php

namespace App\Form\Fields;

class RadioField extends Field
{
    public static function name(): string
    {
        return 'Radio';
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
