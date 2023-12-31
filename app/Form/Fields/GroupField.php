<?php

namespace App\Form\Fields;

use App\Group;
use App\Group\Enums\Level;
use Faker\Generator;
use Illuminate\Validation\Rule;

class GroupField extends Field
{
    public static function name(): string
    {
        return 'Gruppierungs-Auswahl';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
            ['key' => 'parent_field', 'default' => null, 'rules' => ['parent_field' => 'present|nullable|string'], 'label' => 'Übergeordnetes Feld'],
            ['key' => 'parent_group', 'default' => null, 'rules' => ['parent_group' => ['present', 'nullable', Rule::in(Group::pluck('id')->toArray())]], 'label' => 'Übergeordnete Gruppierung'],
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
            'parent_field' => null,
            'parent_group' => null,
        ];
    }
}
