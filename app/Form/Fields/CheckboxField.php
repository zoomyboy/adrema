<?php

namespace App\Form\Fields;

use Faker\Generator;

class CheckboxField extends Field
{
    public static function name(): string
    {
        return 'Checkbox';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'description', 'default' => '', 'rules' => ['description' => 'required|string'], 'label' => 'Beschreibung'],
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
        ];
    }

    public static function default()
    {
        return false;
    }

    public static function fake(Generator $faker): array
    {
        return [];
    }
}
