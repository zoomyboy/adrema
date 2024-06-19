<?php

namespace App\Form\Fields;

use App\Form\Data\FieldCollection;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Presenters\NamiPresenter;
use App\Form\Presenters\Presenter;
use App\Member\Member;
use Faker\Generator;

class NamiField extends Field
{

    public static function name(): string
    {
        return 'NaMi-Mitglieder';
    }

    public static function meta(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function default(): array
    {
        return [];
    }

    public static function fake(Generator $faker): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationRules(Form $form): array
    {
        $rules = [$this->key => 'present|array'];
        $inputMembers = request($this->key);

        foreach ($form->getFields()->noNamiField()->forMembers() as $field) {
            foreach ($field->getRegistrationRules($form) as $ruleKey => $rule) {
                foreach ($inputMembers as $memberIndex => $inputMember) {
                    $rules[$this->key . '.' . $memberIndex . '.' . $ruleKey] = !$inputMember['id'] || $field->namiType === null ? $rule : '';
                }
            }
        }

        return [
            $this->key . '.*.id' => ['nullable', 'numeric', 'exists:members,mitgliedsnr'],
            ...$rules,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(Form $form): array
    {
        $rules = [];
        $inputMembers = request($this->key);

        if (!is_array($inputMembers)) {
            return [];
        }

        foreach ($form->getFields()->noNamiField()->forMembers() as $field) {
            foreach ($field->getRegistrationRules($form) as $ruleKey => $rule) {
                foreach ($inputMembers as $memberIndex => $inputMember) {
                    $message = $field->name . ' für ein Mitglied';
                    $rules = array_merge(
                        $rules,
                        str($ruleKey)->contains('*')
                            ? collect(request($this->key . '.' . $memberIndex . '.' . $field->key))
                            ->mapWithKeys(fn ($value, $key) => [$this->key . '.' . $memberIndex . '.' . str($ruleKey)->replace('*', $key) => $message])
                            ->toArray()
                            : [$this->key . '.' . $memberIndex . '.' . $ruleKey => $message]
                    );
                }
            }
        }

        foreach ($inputMembers as $memberIndex => $inputMember) {
            $rules[$this->key . '.' . $memberIndex . '.id'] = 'Mitglied Nr ' . $inputMember['id'];
        }

        return [
            $this->key => $this->name,
            ...$rules,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationMessages(Form $form): array
    {
        return [];
    }

    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
        foreach ($input[$this->key] as $memberData) {
            $member = Member::firstWhere(['mitgliedsnr' => $memberData['id']]);
            $data = [];
            foreach (FieldCollection::fromRequest($form, $memberData) as $field) {
                $data[$field->key] = $field->namiType === null || $member === null
                    ? $field->value
                    : $field->namiType->getMemberAttribute($member, $form);
            }

            $data[$this->key] = [];
            $form->participants()->create(['data' => $data, 'mitgliedsnr' => $memberData['id'], 'parent_id' => $participant->id]);
        }
    }

    public function getPresenter(): Presenter
    {
        return app(NamiPresenter::class);
    }
}
