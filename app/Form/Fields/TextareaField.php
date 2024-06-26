<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Faker\Generator;

class TextareaField extends Field
{
    public bool $required;
    public int $rows;

    public static function name(): string
    {
        return 'Textarea';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'rows', 'default' => 5, 'rules' => ['rows' => 'present|integer|gt:0'], 'label' => 'Zeilen'],
            ['key' => 'required', 'default' => true, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
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

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {
        return [$this->key => $this->required ? ['required', 'string'] : ['nullable', 'string']];
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
