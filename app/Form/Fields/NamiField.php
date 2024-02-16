<?php

namespace App\Form\Fields;

use App\Form\Models\Form;
use App\Form\Models\Participant;
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
        return [$this->key => []];
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

    public function afterRegistration(Form $form, Participant $participant, array $input): void
    {
        $memberIds = array_column($input[$this->key], 'id');

        foreach ($memberIds as $memberId) {
            $member = Member::firstWhere(['mitgliedsnr' => $memberId]);
            $data = [];
            foreach ($form->getFields() as $field) {
                $field = Field::fromConfig($field);

                $data[$field->key] = $field->namiType === null
                    ? $field->default()
                    : $field->namiType->getMemberAttribute($member);
            }

            $data[$this->key] = [];
            $form->participants()->create(['data' => $data]);
        }
    }
}
