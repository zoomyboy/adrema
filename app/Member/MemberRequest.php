<?php

namespace App\Member;

use App\Activity;
use App\Group;
use App\Invoice\BillKind;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Actions\NamiDeleteMemberAction;
use App\Member\Actions\NamiPutMemberAction;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
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
                if (!$this->input('has_nami')) {
                    return false;
                }

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
            'country_id' => 'required|exists:countries,id',
            'email' => 'nullable|email',
            'email_parents' => 'nullable|email',
            'bill_kind' => ['nullable', Rule::in(BillKind::values())],
            'confession_id' => 'nullable|exists:confessions,id',
            'ps_at' => 'nullable|date_format:Y-m-d',
            'more_ps_at' => 'nullable|date_format:Y-m-d',
            'has_svk' => 'boolean',
            'has_vk' => 'boolean',
            'efz' => 'nullable|date_format:Y-m-d',
            'without_education_at' => 'nullable|date_format:Y-m-d',
            'without_efz_at' => 'nullable|date_format:Y-m-d',
            'recertified_at' => 'nullable|date_format:Y-m-d',
            'multiply_pv' => 'boolean',
            'multiply_more_pv' => 'boolean',
            'send_newspaper' => 'boolean',
            'main_phone' => ['nullable', new ValidPhoneRule('Telefon (Eltern)')],
            'mobile_phone' => ['nullable', new ValidPhoneRule('Handy (Eltern)')],
            'invoice_address' => '',
            'gender_id' => 'nullable|exists:genders,id',
            'region_id' => 'nullable|exists:regions,id',
            'children_phone' => ['nullable', new ValidPhoneRule('Telefon (Kind)')],
            'fax' => ['nullable', new ValidPhoneRule('Fax')],
            'other_country' => '',
            'salutation' => '',
            'comment' => '',
            'keepdata' => 'boolean',
            'bank_account' => 'array',
            'bank_account.iban' => 'nullable|string|max:255',
            'bank_account.bic' => 'nullable|string|max:255',
            'bank_account.blz' => 'nullable|string|max:255',
            'bank_account.bank_name' => 'nullable|string|max:255',
            'bank_account.person' => 'nullable|string|max:255',
            'bank_account.account_number' => 'nullable|string|max:255',
        ];
    }

    public function persistCreate(NamiSettings $settings): void
    {
        $member = new Member([
            ...$this->dataToInsert(),
            'group_id' => Group::where('nami_id', $settings->default_group_id)->firstOrFail()->id,
        ]);
        $member->updatePhoneNumbers()->save();
        $member->bankAccount->update($this->validated('bank_account'));

        if ($this->input('has_nami')) {
            $this->storeFreshMemberInNami($member);
        }
        ResyncAction::dispatch();
    }

    protected function storeFreshMemberInNami(Member $member): void
    {
        NamiPutMemberAction::run(
            $member->fresh(),
            Activity::findOrFail($this->input('first_activity_id')),
            Subactivity::find($this->input('first_subactivity_id')),
        );
    }

    public function persistUpdate(Member $member): void
    {
        $member->fill($this->dataToInsert())->updatePhoneNumbers();

        $namiSync = $member->isDirty(Member::$namiFields);

        $member->save();
        $member->bankAccount->update($this->validated('bank_account'));

        if ($this->input('has_nami') && null === $member->nami_id) {
            $this->storeFreshMemberInNami($member);
        }
        if ($this->input('has_nami') && null !== $member->nami_id && $namiSync) {
            NamiPutMemberAction::run($member->fresh(), null, null);
        }
        if (!$this->input('has_nami') && null !== $member->nami_id) {
            NamiDeleteMemberAction::dispatch($member->nami_id);
        }
        ResyncAction::dispatch();
    }

    public function withValidator(Validator $validator): void
    {
        $this->namiIfElse($validator, 'birthday', 'date|required');
        $this->namiIfElse($validator, 'nationality_id', 'required|exists:nationalities,id');
        $this->namiIfElse($validator, 'address', 'required|max:255');
        $this->namiIfElse($validator, 'zip', 'required|numeric');
        $this->namiIfElse($validator, 'location', 'required|max:255');
        $this->namiIfElse($validator, 'joined_at', 'date|required');
        $this->namiIfStoring($validator, 'first_activity_id', 'required|exclude|exists:activities,id');
        $this->namiIfStoring($validator, 'first_subactivity_id', 'required|exclude|exists:subactivities,id');
    }

    private function namiIfElse(Validator $validator, string $attribute, string $rules): void
    {
        $request = request();
        $when = fn () => true === $request->input('has_nami');
        $notWhen = fn () => true !== $request->input('has_nami');
        $validator->sometimes($attribute, $rules, $when);
        $validator->sometimes($attribute, 'present', $notWhen);
    }

    private function namiIfStoring(Validator $validator, string $attribute, string $rules): void
    {
        $request = request();
        /** @var ?Member */
        $member = request()->route('member');
        $when = fn () => true === $request->input('has_nami') && ($member === null || !$member->has_nami);
        $validator->sometimes($attribute, $rules, $when);
    }

    /**
     * @return array<string, mixed>
     */
    protected function dataToInsert(): array
    {
        return $this->safe()->except('bank_account');
    }
}
