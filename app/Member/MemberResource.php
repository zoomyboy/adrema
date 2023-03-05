<?php

namespace App\Member;

use App\Course\Resources\CourseMemberResource;
use App\Group;
use App\Lib\HasMeta;
use App\Member\Resources\NationalityResource;
use App\Member\Resources\RegionResource;
use App\Membership\MembershipResource;
use App\Payment\PaymentResource;
use App\Payment\SubscriptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Member\Member
 */
class MemberResource extends JsonResource
{
    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'address' => $this->address,
            'zip' => $this->zip,
            'location' => $this->location,
            'send_newspaper' => $this->send_newspaper,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'birthday_human' => $this->birthday?->format('d.m.Y'),
            'joined_at' => $this->joined_at->format('Y-m-d'),
            'joined_at_human' => $this->joined_at->format('d.m.Y'),
            'id' => $this->id,
            'subscription_id' => $this->subscription_id,
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'gender_id' => $this->gender_id,
            'gender_name' => $this->gender?->name ?: 'keine Angabe',
            'fullname' => ($this->gender ? $this->gender->salutation.' ' : '').$this->fullname,
            'further_address' => $this->further_address,
            'work_phone' => $this->work_phone,
            'mobile_phone' => $this->mobile_phone,
            'main_phone' => $this->main_phone,
            'email' => $this->email,
            'email_parents' => $this->email_parents,
            'fax' => $this->fax,
            'country_id' => $this->country_id,
            'region_id' => $this->region_id,
            'nationality_id' => $this->nationality_id,
            'other_country' => $this->other_country,
            'confession_id' => $this->confession_id,
            'letter_address' => $this->letter_address,
            'bill_kind' => optional($this->bill_kind)->value,
            'bill_kind_name' => optional($this->bill_kind)->value,
            'has_nami' => null !== $this->nami_id,
            'children_phone' => $this->children_phone,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'memberships' => MembershipResource::collection($this->whenLoaded('memberships')),
            'pending_payment' => $this->pending_payment ? number_format($this->pending_payment / 100, 2, ',', '.').' €' : null,
            'age_group_icon' => $this->ageGroupMemberships->first()?->subactivity->slug,
            'courses' => CourseMemberResource::collection($this->whenLoaded('courses')),
            'nationality' => new NationalityResource($this->whenLoaded('nationality')),
            'region' => new RegionResource($this->whenLoaded('region')),
            'full_address' => $this->fullAddress,
            'efz' => $this->efz?->format('Y-m-d'),
            'efz_human' => $this->efz?->format('d.m.Y') ?: null,
            'ps_at_human' => $this->ps_at?->format('d.m.Y') ?: null,
            'more_ps_at_human' => $this->more_ps_at?->format('d.m.Y') ?: null,
            'without_education_at_human' => $this->without_education_at?->format('d.m.Y') ?: null,
            'without_efz_at_human' => $this->without_efz_at?->format('d.m.Y') ?: null,
            'efz_link' => $this->getEfzLink(),
            'ps_at' => $this->ps_at?->format('Y-m-d'),
            'more_ps_at' => $this->more_ps_at?->format('Y-m-d'),
            'has_svk' => $this->has_svk,
            'nami_id' => $this->nami_id,
            'has_vk' => $this->has_vk,
            'without_education_at' => $this->without_education_at?->format('Y-m-d'),
            'without_efz_at' => $this->without_efz_at?->format('Y-m-d'),
            'multiply_pv' => $this->multiply_pv,
            'multiply_more_pv' => $this->multiply_more_pv,
            'age' => $this->getModel()->getAge(),
            'is_leader' => $this->leaderMemberships->count() > 0,
            'group_id' => $this->group_id,
            'salutation' => $this->salutation,
            'mitgliedsnr' => $this->mitgliedsnr,
            'comment' => $this->comment,
            'links' => [
                'show' => route('member.show', ['member' => $this->getModel()]),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'groups' => Group::select('name', 'id')->get(),
        ];
    }
}
