<?php

namespace App\Form\Fields;

use Faker\Generator;

class CheckboxesField extends Field
{
    public static function name(): string
    {
        return 'Checkboxes';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'options', 'default' => [], 'rules' => ['options' => 'array', 'options.*' => 'string'], 'label' => 'Optionen'],
        ];
    }

    public static function default()
    {
        return [];
    }

    public static function fake(Generator $faker): array
    {
        return [];
    }
}
