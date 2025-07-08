<?php

namespace App\Form\Requests;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Data\MemberData;
use App\Contribution\Documents\ContributionDocument;
use App\Country;
use App\Form\Enums\SpecialType;
use App\Form\Models\Form;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Spatie\LaravelData\Data;

class FormCompileRequest extends Data implements HasContributionData {

    public function __construct(public Form $form) {}

    /**
     * @return class-string<ContributionDocument>
     */
    public function type(): string
    {
        $payload = json_decode(rawurldecode(base64_decode(request()->input('payload', ''))), true);

        return $payload['type'];
    }

    public function dateFrom(): Carbon
    {
        return $this->form->from;
    }

    public function dateUntil(): Carbon
    {
        return $this->form->to;
    }

    public function zipLocation(): string
    {
        return $this->form->zip.' '.$this->form->location;
    }

    public function eventName(): string
    {
        return $this->form->name;
    }

    public function members(): Collection
    {
        $members = [];
        $fields = [
            [SpecialType::FIRSTNAME, 'firstname'],
            [SpecialType::LASTNAME, 'lastname'],
            [SpecialType::BIRTHDAY, 'birthday'],
            [SpecialType::ADDRESS, 'address'],
            [SpecialType::ZIP, 'zip'],
            [SpecialType::LOCATION, 'location'],
            [SpecialType::GENDER, 'gender']
        ];

        foreach ($this->form->participants as $participant) {
            $member = [];
            foreach ($fields as [$type, $name]) {
                $f = $this->form->getFields()->findBySpecialType($type);
                if (!$f) {
                    continue;
                }
                $member[$name] = $participant->getFields()->find($f)->value;
            }

            $members[] = [
                'is_leader' => false,
                'gender' => 'weiblich',
                ...$member,
            ];
        }

        return MemberData::fromApi($members);
    }

    public function country(): ?Country
    {
        return Country::first();
    }

    public function validateContribution(): void
    {
        Validator::make($this->form->toArray(), [
            'zip' => 'required',
            'location' => 'required'
        ])
            ->after(function($validator) {
                foreach ($this->type()::requiredFormSpecialTypes() as $type) {
                    if (!$this->form->getFields()->hasSpecialType($type)) {
                        $validator->errors()->add($type->name, 'Kein Feld für ' . $type->value . ' vorhanden.');
                    }
                }
                if ($this->form->participants->count() === 0) {
                    $validator->errors()->add('participants',  'Veranstaltung besitzt noch keine Teilnehmer*innen.');
                }
            })
            ->validate();
    }
}
