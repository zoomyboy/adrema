<?php

namespace App\Member;

use App\Activity;
use App\Group;
use App\Member\Actions\NamiPutMemberAction;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ...'POST' === $this->method() ? [
                'first_activity' => 'exclude|required',
                'first_subactivity' => 'exclude|required',
            ] : [],
            'subscription_id' => Rule::requiredIf(function () {
                if ('POST' != $this->method()) {
                    return false;
                }

                if (!$this->input('first_activity_id')) {
                    return true;
                }

                return Str::contains(Activity::findOrFail($this->input('first_activity_id'))->name, '€');
            }),
            'firstname' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'zip' => 'required|numeric',
            'location' => 'required',
            'birthday' => 'date|required',
            'region_id' => 'nullable|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'nationality_id' => 'required|exists:nationalities,id',
            'email' => 'nullable|email',
            'email_parents' => 'nullable|email',
            'bill_kind_id' => 'nullable|exists:bill_kinds,id',
            'joined_at' => 'date|required',
            'confession_id' => 'nullable|exists:confessions,id',
            'ps_at' => 'nullable|date_format:Y-m-d',
            'more_ps_at' => 'nullable|date_format:Y-m-d',
            'has_svk' => 'boolean',
            'has_vk' => 'boolean',
            'efz' => 'nullable|date_format:Y-m-d',
            'without_education_at' => 'nullable|date_format:Y-m-d',
            'without_efz_at' => 'nullable|date_format:Y-m-d',
            'multiply_pv' => 'boolean',
            'multiply_more_pv' => 'boolean',
            'send_newspaper' => 'boolean',
            'main_phone' => '',
            'mobile_phone' => '',
            'letter_address' => '',
            'gender_id' => 'nullable|exists:genders,id',
            'region_id' => 'nullable|exists:regions,id',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'children_phone' => '',
            'fax' => '',
            'other_country' => '',
        ];
    }

    public function persistCreate(NamiSettings $settings): void
    {
        $member = Member::create([
            ...$this->validated(),
            'group_id' => Group::where('nami_id', $settings->default_group_id)->firstOrFail()->id,
        ]);
        if ($this->input('has_nami')) {
            NamiPutMemberAction::run(
                $member,
                Activity::findOrFail($this->input('first_activity_id')),
                Subactivity::find($this->input('first_subactivity_id')),
            );
        }
    }

    public function persistUpdate(Member $member): void
    {
        $member->fill($this->validated());

        $namiSync = $member->isDirty(Member::$namiFields);

        $member->save();

        if ($this->input('has_nami') && null === $member->nami_id) {
            NamiPutMemberAction::run($member->fresh(), null, null);
        }
        if ($this->input('has_nami') && null !== $member->nami_id && $namiSync) {
            NamiPutMemberAction::run($member->fresh(), null, null);
        }
        if (!$this->input('has_nami') && null !== $member->nami_id) {
            DeleteJob::dispatch($member->nami_id);
        }
    }
}
