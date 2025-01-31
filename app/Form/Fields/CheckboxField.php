<?php

namespace App\Form\Fields;

use App\Form\Contracts\Filterable;
use App\Form\Matchers\BooleanMatcher;
use App\Form\Matchers\Matcher;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\BooleanPresenter;
use App\Form\Presenters\Presenter;
use Faker\Generator;

class CheckboxField extends Field implements Filterable
{
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
            ['key' => 'required', 'default' => true, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
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
    public function getRegistrationRules(Form $form): array
    {
        return [
            $this->key => $this->required ? ['boolean', 'accepted'] : ['present', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(Form $form): array
    {
        return [
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

    /**
     * @inheritdoc
     */
    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
    }

    public function getPresenter(): Presenter
    {
        return app(BooleanPresenter::class);
    }

    public function getMatcher(): Matcher
    {
        return app(BooleanMatcher::class);
    }

    public function filter($value): string
    {
        $asString = $value ? 'true' : 'false';

        return "{$this->key} = $asString";
    }
}
