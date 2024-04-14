<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Faker\Generator;

class NumberField extends Field
{

    public bool $required;
    public ?int $min;
    public ?int $max;

    public static function name(): string
    {
        return 'Numerisch';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'required', 'default' => true, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
            ['key' => 'min', 'default' => null, 'rules' => ['min' => 'present|nullable|numeric'], 'label' => 'minimaler Wert'],
            ['key' => 'max', 'default' => null, 'rules' => ['min' => 'present|nullable|numeric'], 'label' => 'maximaler Wert'],
        ];
    }

    public static function default(): ?int
    {
        return null;
    }

    public static function fake(Generator $faker): array
    {
        return [
            'required' => $faker->boolean(),
            'min' => $faker->numberBetween(0, 100),
            'max' => $faker->numberBetween(0, 100),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {
        $minmax = [];

        if ($this->min !== null) {
            $minmax[] = 'gte:' . $this->min;
        }

        if ($this->max !== null) {
            $minmax[] = 'lte:' . $this->max;
        }

        return [$this->key => $this->required ? ['required', 'integer', ...$minmax] : ['nullable', 'integer', ...$minmax]];
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
