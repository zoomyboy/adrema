<?php

namespace App\Member;

use App\Activity;
use App\Group;
use App\Setting\NamiSettings;
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
            'first_activity_id' => Rule::requiredIf(fn () => 'POST' == $this->method()),
            'first_subactivity_id' => Rule::requiredIf(fn () => 'POST' == $this->method()),
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
            'efz' => 'nullable|date',
        ];
    }

    public function persistCreate(NamiSettings $settings): void
    {
        $this->merge(['group_id' => Group::where('nami_id', $settings->default_group_id)->firstOrFail()->id]);
        $member = Member::create($this->input());
        if ($this->input('has_nami')) {
            CreateJob::dispatch($member);
        }
    }

    public function persistUpdate(Member $member): void
    {
        $member->update($this->input());

        if ($this->input('has_nami') && null === $member->nami_id) {
            CreateJob::dispatch($member);
        }
        if ($this->input('has_nami') && null !== $member->nami_id) {
            UpdateJob::dispatch($member->fresh());
        }
        if (!$this->input('has_nami') && null !== $member->nami_id) {
            DeleteJob::dispatch($member);
        }
    }
}
