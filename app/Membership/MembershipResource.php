<?php

namespace App\Membership;

use App\Activity;
use App\Lib\HasMeta;
use App\Member\Data\NestedGroup;
use App\Member\Member;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Member\Membership
 */
class MembershipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'activity_id' => $this->activity_id,
            'activity_name' => $this->activity->name,
            'subactivity_id' => $this->subactivity_id,
            'subactivity_name' => $this->subactivity?->name,
            'human_date' => $this->from->format('d.m.Y'),
            'promised_at' => $this->promised_at?->format('Y-m-d'),
            'is_active' => $this->isActive(),
            'links' => [
                'update' => route('membership.update', ['membership' => $this->getModel()]),
                'destroy' => route('membership.destroy', ['membership' => $this->getModel()]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function memberMeta(Member $member): array
    {
        $activities = Activity::with('subactivities')->get();

        return [
            'links' => [
                'store' => route('member.membership.store', ['member' => $member]),
            ],
            'groups' => NestedGroup::cacheForSelect(),
            'activities' => $activities->map(fn ($activity) => ['id' => $activity->id, 'name' => $activity->name]),
            'subactivities' => $activities->mapWithKeys(fn ($activity) => [$activity->id => $activity->subactivities->map(fn ($subactivity) => ['id' => $subactivity->id, 'name' => $subactivity->name, 'is_age_group' => $subactivity->is_age_group])]),
            'default' => [
                'group_id' => $member->group_id,
                'activity_id' => null,
                'subactivity_id' => null,
                'promised_at' => null,
            ],
        ];
    }
}
