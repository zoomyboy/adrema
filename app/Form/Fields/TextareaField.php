<?php

namespace App\Form\Fields;

class TextareaField extends Field
{
    public static function name(): string
    {
        return 'Textarea';
    }

    public static function meta(): array
    {
        return [
            'rows' => 5,
        ];
    }

    public static function default(): string
    {
        return '';
    }
}
