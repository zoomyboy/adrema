<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\GroupPresenter;
use App\Form\Presenters\Presenter;
use App\Group;
use App\Group\Enums\Level;
use Faker\Generator;
use Illuminate\Validation\Rule;

class GroupField extends Field
{
    public bool $required;
    public ?string $parentField = null;
    public ?int $parentGroup = null;
    public bool $hasEmptyOption;
    public ?string $emptyOptionValue;

    public static function name(): string
    {
        return 'Gruppierungs-Auswahl';
    }

    public static function meta(): array
    {
        return [
            ['key' => 'required', 'default' => true, 'rules' => ['required' => 'present|boolean'], 'label' => 'Erforderlich'],
            ['key' => 'parent_field', 'default' => null, 'rules' => ['parent_field' => 'present|nullable|string'], 'label' => 'Übergeordnetes Feld'],
            ['key' => 'parent_group', 'default' => null, 'rules' => ['parent_group' => ['present', 'nullable', Rule::in(Group::pluck('id')->toArray())]], 'label' => 'Übergeordnete Gruppierung'],
            ['key' => 'has_empty_option', 'default' => false, 'rules' => ['has_empty_option' => ['present', 'boolean']], 'label' => 'Leere Option erlauben'],
            ['key' => 'empty_option_value', 'default' => '', 'rules' => ['empty_option_value' => ['present', 'nullable', 'string']], 'label' => 'Leere Option'],
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
            'parent_field' => null,
            'parent_group' => null,
            'has_empty_option' => $faker->boolean(),
            'empty_option_value' => '',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {

        $rules = [$this->required ? 'required' : 'nullable'];

        $rules[] = 'integer';

        if ($this->parentGroup) {
            $rules[] = Rule::in(Group::find($this->parentGroup)->children()->pluck('id')->push(-1));
        }

        if ($this->parentField && request()->input($this->parentField) && request()->input($this->parentField) !== -1) {
            $rules[] = Rule::in(Group::find(request()->input($this->parentField))->children()->pluck('id')->push(-1));
        }

        return [$this->key => $rules];
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

    public function getPresenter(): Presenter
    {
        return app(GroupPresenter::class)->field($this);
    }

    /**
     * @inheritdoc
     */
    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
    }
}
