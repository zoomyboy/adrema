<?php

namespace App\Form\Fields;

use Faker\Generator;

class TextField extends Field
{
    public static function name(): string
    {
        return 'Text';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
        ];
    }

    public static function default(): string
    {
        return '';
    }

    public static function fake(Generator $faker): array
    {
        return [
            'required' => $faker->boolean(),
        ];
    }
}
