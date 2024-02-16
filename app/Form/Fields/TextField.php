<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Faker\Generator;

class TextField extends Field
{

    public bool $required;

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

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(): array
    {
        return [$this->key => $this->required ? ['required', 'string'] : ['nullable', 'string']];
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

    /**
     * @inheritdoc
     */
    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
    }
}
