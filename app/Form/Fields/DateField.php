<?php

namespace App\Form\Fields;

use App\Form\Contracts\Displayable;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\DatePresenter;
use App\Form\Presenters\Presenter;
use Carbon\Carbon;
use Faker\Generator;

class DateField extends Field
{

    public bool $required;
    public bool $maxToday;

    public static function name(): string
    {
        return 'Datum';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'required', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
            ['key' => 'max_today', 'default' => false, 'rules' => ['required' => 'present|boolean'], 'label' => 'Nur daten bis heute erlauben'],
        ];
    }

    public static function default(): ?string
    {
        return null;
    }

    public static function fake(Generator $faker): array
    {
        return [
            'required' => $faker->boolean(),
            'max_today' => $faker->boolean(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(): array
    {
        $rules = [$this->required ? 'required' : 'nullable'];

        $rules[] = 'date';

        if ($this->maxToday) {
            $rules[] = 'before_or_equal:' . now()->format('Y-m-d');
        }

        return [$this->key => $rules];
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
        return [
            $this->key . '.before_or_equal' => $this->name . ' muss ein Datum vor oder gleich dem ' . now()->format('d.m.Y') . ' sein.',
        ];
    }

    public function getPresenter(): Presenter
    {
        return app(DatePresenter::class);
    }

    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
    }
}
