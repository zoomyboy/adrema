<?php

namespace App\Membership;

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
            'human_date' => $this->created_at->format('d.m.Y'),
            'promised_at' => $this->promised_at?->format('Y-m-d'),
        ];
    }
}
