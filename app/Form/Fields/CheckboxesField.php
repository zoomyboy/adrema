<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\EnumPresenter;
use App\Form\Presenters\Presenter;
use Faker\Generator;
use Illuminate\Validation\Rule;

class CheckboxesField extends Field
{
    /** @var array<int, string> */
    public array $options;
    public ?int $min;
    public ?int $max;

    public static function name(): string
    {
        return 'Checkboxes';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'options', 'default' => [], 'rules' => ['options' => 'array', 'options.*' => 'required|string'], 'label' => 'Optionen'],
            ['key' => 'min', 'default' => null, 'rules' => ['min' => 'present'], 'label' => 'minimale Anzahl'],
            ['key' => 'max', 'default' => null, 'rules' => ['max' => 'present'], 'label' => 'maximale Anzahl'],
        ];
    }

    public static function default()
    {
        return [];
    }

    public static function fake(Generator $faker): array
    {
        return [
            'options' => $faker->words(4),
            'min' => null,
            'max' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {
        $globalRules = ['array'];

        if ($this->min > 0) {
            $globalRules[] = 'min:' . $this->min;
        }

        if ($this->max > 0) {
            $globalRules[] = 'max:' . $this->max;
        }

        return [
            $this->key => $globalRules,
            $this->key . '.*' => ['string', Rule::in($this->options)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(Form $form): array
    {
        return [
            ...collect($this->options)->mapWithKeys(fn ($option, $key) => [$this->key . '.' . $key => $this->name])->toArray(),
            $this->key => $this->name,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationMessages(Form $form): array
    {
        return [];
    }

    public function getPresenter(): Presenter
    {
        return app(EnumPresenter::class);
    }

    /**
     * @inheritdoc
     */
    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
    }
}
