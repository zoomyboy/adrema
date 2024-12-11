<?php

namespace App\Form\Fields;

use App\Form\Contracts\Filterable;
use App\Form\Matchers\Matcher;
use App\Form\Matchers\SingleValueMatcher;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use Faker\Generator;
use Illuminate\Validation\Rule;

class RadioField extends Field implements Filterable
{
    public bool $required;
    /** @var array<int, string> */
    public array $options;
    public bool $allowcustom;

    public static function name(): string
    {
        return 'Radio';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'options', 'default' => [], 'rules' => ['options' => 'present|array', 'options.*' => 'required|string'], 'label' => 'Optionen'],
            ['key' => 'required', 'default' => true, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
            ['key' => 'allowcustom', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Eigene Option erlauben'],
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
            'allowcustom' => $faker->boolean(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {
        $rules = $this->required ? ['required', 'string'] : ['nullable', 'string'];

        if (!$this->allowcustom) {
            $rules[] = Rule::in($this->options);
        }

        return [
            $this->key => $rules
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(Form $form): array
    {
        return [$this->key => $this->name];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationMessages(Form $form): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
    }

    public function getMatcher(): Matcher
    {
        return app(SingleValueMatcher::class);
    }

    public function filter($value): string
    {
        if (is_null($value)) {
            return "{$this->key} IS NULL";
        }

        return $this->key . ' = \'' . $value . '\'';
    }
}
