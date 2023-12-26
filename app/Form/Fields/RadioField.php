<?php

namespace App\Form\Fields;

use Faker\Generator;

class RadioField extends Field
{
    public static function name(): string
    {
        return 'Radio';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'options', 'default' => [], 'rules' => ['options' => 'present|array', 'options.*' => 'required|string'], 'label' => 'Optionen'],
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
        ];
    }

    public static function default()
    {
        return null;
    }

    public static function fake(Generator $faker): array
    {
        return [];
    }
}
