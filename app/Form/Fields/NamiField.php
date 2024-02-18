<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Member\Member;
use Faker\Generator;
use Generator as LazyGenerator;

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
    public function getRegistrationRules(): array
    {
        $rules = [];
        $fields = request()->route('form')->getFields();

        $c = collect($fields)->filter(fn ($field) => $field['nami_type'] === null)->filter(fn ($field) => $field['type'] !== class_basename(static::class))->map(fn ($field) => Field::fromConfig($field)->getRegistrationRules());

        foreach ($c as $field) {
            foreach ($field as $ruleKey => $rule) {
                $rules[$this->key . '.*.' . $ruleKey] = $rule;
            }
        }

        return [
            $this->key . '.*.id' => ['required', 'numeric', 'exists:members,mitgliedsnr'],
            ...$rules,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRegistrationAttributes(): array
    {
        $rules = [];
        $fields = request()->route('form')->getFields();
        $inputMembers = request($this->key);

        $c = collect($fields)->filter(fn ($field) => $field['type'] !== class_basename(static::class))->map(fn ($field) => Field::fromConfig($field));

        foreach ($c as $field) {
            foreach ($field->getRegistrationRules() as $ruleKey => $rule) {
                foreach ($inputMembers as $memberIndex => $inputMember) {
                    if (str($ruleKey)->contains('*')) {
                        foreach (request($this->key . '.' . $memberIndex . '.' . $field->key) as $i => $k) {
                            $rules[$this->key . '.' . $memberIndex . '.' . str($ruleKey)->replace('*', $i)] = $field->name . ' für Mitglied Nr ' . $inputMember['id'];
                        }
                    } else {
                        $rules[$this->key . '.' . $memberIndex . '.' . $ruleKey] = $field->name . ' für Mitglied Nr ' . $inputMember['id'];
                    }
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
    public function getRegistrationMessages(): array
    {
        return [];
    }

    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
        foreach ($input[$this->key] as $memberData) {
            $member = Member::firstWhere(['mitgliedsnr' => $memberData['id']]);
            $data = [];
            foreach ($form->getFields() as $field) {
                $field = Field::fromConfig($field);

                $data[$field->key] = $field->namiType === null
                    ? data_get($memberData, $field->key, $field->default())
                    : $field->namiType->getMemberAttribute($member);
            }

            $data[$this->key] = [];
            $form->participants()->create(['data' => $data]);
        }
    }
}
