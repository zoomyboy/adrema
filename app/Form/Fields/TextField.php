<?php

namespace App\Form\Fields;

class TextField extends Field
{
    public static function name(): string
    {
        return 'Text';
    }

    public static function meta(): array
    {
        return [];
    }

    public static function default(): string
    {
        return '';
    }
}
