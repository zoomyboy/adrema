<?php

namespace App\Form\Fields;

use Faker\Generator;
use Illuminate\Validation\Rule;

class RadioField extends Field
{
    public string $name;
    public string $key;
    public bool $required;
    /** @var array<int, string> */
    public array $options;

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
        return [
            'options' => $faker->words(4),
            'required' => $faker->boolean(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(): array
    {
        return [
            $this->key => $this->required ? ['required', 'string', Rule::in($this->options)] : ['nullable', 'string', Rule::in($this->options)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(): array
    {
        return [$this->key => $this->name];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationMessages(): array
    {
        return [];
    }
}
