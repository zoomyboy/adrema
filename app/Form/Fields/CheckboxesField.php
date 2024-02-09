<?php

namespace App\Form\Fields;

use App\Form\Presenters\EnumPresenter;
use App\Form\Presenters\Presenter;
use Faker\Generator;
use Illuminate\Validation\Rule;

class CheckboxesField extends Field
{
    /** @var array<int, string> */
    public array $options;

    public static function name(): string
    {
        return 'Checkboxes';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'options', 'default' => [], 'rules' => ['options' => 'array', 'options.*' => 'required|string'], 'label' => 'Optionen'],
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(): array
    {
        return [
            $this->key => 'array',
            $this->key . '.*' => ['string', Rule::in($this->options)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(): array
    {
        return [
            ...collect($this->options)->mapWithKeys(fn ($option, $key) => [$this->key . '.' . $key => $this->name])->toArray(),
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

    public function getPresenter(): Presenter
    {
        return app(EnumPresenter::class);
    }
}
