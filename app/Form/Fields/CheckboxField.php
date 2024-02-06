<?php

namespace App\Form\Fields;

use Faker\Generator;
use Illuminate\Validation\Rule;

class CheckboxField extends Field
{
    public string $name;
    public string $key;
    public bool $required;
    public string $description;

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
        return [
            'description' => $faker->text(),
            'required' => $faker->boolean(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(): array
    {
        return [
            $this->key => $this->required ? ['boolean', 'accepted'] : ['present', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(): array
    {
        return [
            $this->key => $this->name,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationMessages(): array
    {
        return [];
    }
}
