<?php

namespace App\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Group;
use Illuminate\Support\Str;
use App\Activity;
use Illuminate\Support\Arr;

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
            'first_activity_id' => Rule::requiredIf(fn() => $this->method() == 'POST'), 
            'first_subactivity_id' => Rule::requiredIf(fn() => $this->method() == 'POST'), 
            'fee_id' => Rule::requiredIf(function() {
                if ($this->method() != 'POST') {
                    return false;
                }
               
                if (!$this->input('first_activity_id')) { return true; }
                
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
        ];
    }

    public function persistCreate() {
        $this->merge(['group_id' => Group::where('nami_id', auth()->user()->getNamiGroupId())->firstOrFail()->id]);
        $m = Member::create($this->input());
    }

    public function persistUpdate(Member $member) {
        $member->update(Arr::except($this->input(), ['first_activity_id', 'first_subactivity_id']));

        if($this->input('has_nami') && $member->nami_id === null) {
            CreateJob::dispatch($member, auth()->user());
        }
        if($this->input('has_nami') && $member->nami_id !== null) {
            UpdateJob::dispatch($member, auth()->user());
        }
    }
}
