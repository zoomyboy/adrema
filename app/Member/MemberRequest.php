<?php

namespace App\Member;

use App\Activity;
use App\Group;
use App\Invoice\BillKind;
use App\Member\Actions\NamiPutMemberAction;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Zoomyboy\Phone\ValidPhoneRule;

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
            'country_id' => 'required|exists:countries,id',
            'email' => 'nullable|email',
            'email_parents' => 'nullable|email',
            'bill_kind' => ['nullable', Rule::in(BillKind::values())],
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
            'main_phone' => ['nullable', new ValidPhoneRule('Telefon (Eltern)')],
            'mobile_phone' => ['nullable', new ValidPhoneRule('Handy (Eltern)')],
            'invoice_address' => '',
            'gender_id' => 'nullable|exists:genders,id',
            'region_id' => 'nullable|exists:regions,id',
            'nationality_id' => 'required|exists:nationalities,id',
            'children_phone' => ['nullable', new ValidPhoneRule('Telefon (Kind)')],
            'fax' => ['nullable', new ValidPhoneRule('Fax')],
            'other_country' => '',
            'salutation' => '',
            'comment' => '',
        ];
    }

    public function persistCreate(NamiSettings $settings): void
    {
        $member = new Member([
            ...$this->validated(),
            'group_id' => Group::where('nami_id', $settings->default_group_id)->firstOrFail()->id,
        ]);
        $member->updatePhoneNumbers()->save();

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
        $member->fill($this->validated())->updatePhoneNumbers();

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
