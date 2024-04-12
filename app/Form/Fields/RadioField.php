<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\Presenter;
use Faker\Generator;
use Illuminate\Validation\Rule;

class RadioField extends Field
{
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
            ['key' => 'required', 'default' => true, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
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
    public function getRegistrationRules(Form $form): array
    {
        return [
            $this->key => $this->required ? ['required', 'string', Rule::in($this->options)] : ['nullable', 'string', Rule::in($this->options)],
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
}
