<?php

namespace App\Membership\Requests;

use App\Activity;
use App\Member\Member;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Foundation\Http\FormRequest;
use Zoomyboy\LaravelNami\Data\Membership;

class StoreRequest extends FormRequest
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
        ];
    }

    public function persist(Member $member, NamiSettings $settings): void
    {
        $from = now()->startOfDay();
        $namiId = $settings->login()->putMembership($member->nami_id, Membership::fromArray([
            'startsAt' => $from,
            'groupId' => $member->group->nami_id,
            'activityId' => Activity::find($this->input('activity_id'))->nami_id,
            'subactivityId' => optional(Subactivity::find($this->input('subactivity_id')))->nami_id,
        ]));

        $member->memberships()->create([
            ...$this->input(),
            ...['nami_id' => $namiId, 'group_id' => $member->group->id, 'from' => $from],
        ]);

        $member->syncVersion();
    }
}
