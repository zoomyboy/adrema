<?php

namespace App\Form\Fields;

use Faker\Generator;

class DateField extends Field
{
    public static function name(): string
    {
        return 'Datum';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
            ['key' => 'max_today', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Nur daten bis heute erlauben'],
        ];
    }

    public static function default(): ?string
    {
        return null;
    }

    public static function fake(Generator $faker): array
    {
        return [
            'required' => $faker->boolean(),
            'max_today' => $faker->boolean(),
        ];
    }
}
