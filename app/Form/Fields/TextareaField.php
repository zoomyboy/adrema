<?php

namespace App\Form\Fields;

use Faker\Generator;

class TextareaField extends Field
{
    public static function name(): string
    {
        return 'Textarea';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'rows', 'default' => 5, 'rules' => ['rows' => 'present|integer|gt:0'], 'label' => 'Zeilen'],
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
            'rows' => $faker->numberBetween(5, 10),
            'required' => $faker->boolean(),
        ];
    }
}
