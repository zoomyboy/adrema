<?php

namespace App\Form\Fields;

use Faker\Generator;

class DropdownField extends Field
{
    public static function name(): string
    {
        return 'Dropdown';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'options', 'default' => [], 'rules' => ['options' => 'present|array', 'options.*' => 'string'], 'label' => 'Optionen'],
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
        ];
    }

    public static function default()
    {
        return null;
    }

    public static function fake(Generator $faker): array
    {
        return [
            'options' => $faker->words(4),
            'required' => $faker->boolean(),
        ];
    }
}
