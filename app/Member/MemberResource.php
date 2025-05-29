<?php

namespace App\Member;

use App\Activity;
use App\Country;
use App\Course\Models\Course;
use App\Course\Resources\CourseMemberResource;
use App\Gender;
use App\Invoice\BillKind;
use App\Invoice\Resources\InvoicePositionResource;
use App\Lib\HasMeta;
use App\Member\Data\NestedGroup;
use App\Member\Resources\BankAccountResource;
use App\Member\Resources\NationalityResource;
use App\Member\Resources\RegionResource;
use App\Membership\MembershipResource;
use App\Nationality;
use App\Payment\Subscription;
use App\Payment\SubscriptionResource;
use App\Region;
use App\Subactivity;
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
            'fullname' => ($this->gender ? $this->gender->salutation . ' ' : '') . $this->fullname,
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
            'pending_payment' => $this->pending_payment ? number_format($this->pending_payment / 100, 2, ',', '.') . ' €' : null,
            'age_group_icon' => $this->ageGroupMemberships->first()?->subactivity->slug,
            'courses' => CourseMemberResource::collection($this->whenLoaded('courses')),
            'memberships' => MembershipResource::collection($this->whenLoaded('memberships')),
            'invoicePositions' => InvoicePositionResource::collection($this->whenLoaded('invoicePositions')),
            'nationality' => new NationalityResource($this->whenLoaded('nationality')),
            'region' => new RegionResource($this->whenLoaded('region')),
            'full_address' => $this->fullAddress,
            'efz' => $this->efz?->format('Y-m-d'),
            'efz_human' => $this->efz?->format('d.m.Y') ?: null,
            'ps_at_human' => $this->ps_at?->format('d.m.Y') ?: null,
            'recertified_at_human' => $this->recertified_at?->format('d.m.Y') ?: null,
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
            'recertified_at' => $this->recertified_at?->format('Y-m-d'),
            'multiply_pv' => $this->multiply_pv,
            'multiply_more_pv' => $this->multiply_more_pv,
            'age' => $this->getModel()->getAge(),
            'is_leader' => $this->leaderMemberships->count() > 0,
            'group_id' => $this->group_id,
            'salutation' => $this->salutation,
            'mitgliedsnr' => $this->mitgliedsnr,
            'comment' => $this->comment,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'group_name' => $this->group->name,
            'keepdata' => $this->keepdata,
            'bank_account' => new BankAccountResource($this->whenLoaded('bankAccount')),
            'links' => [
                'membership_index' => route('member.membership.index', ['member' => $this->getModel()]),
                'invoiceposition_index' => route('member.invoice-position.index', ['member' => $this->getModel()]),
                'course_index' => route('member.course.index', ['member' => $this->getModel()]),
                'show' => route('member.show', ['member' => $this->getModel()]),
                'edit' => route('member.edit', ['member' => $this->getModel()]),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        if (request()->header('X-Meta') === 'false') {
            return [];
        }

        if (request()->header('X-Inertia-Partial-Data', '') !== '' && !str_contains(request()->header('X-Inertia-Partial-Data', ''), 'meta')) {
            return [];
        }

        $activities = Activity::with('subactivities')->get();
        $createActivities = Activity::remote()->with(['subactivities' => fn($q) => $q->remote()])->get();

        return [
            'filterActivities' => Activity::where('is_filterable', true)->get()->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            'filterSubactivities' => Subactivity::where('is_filterable', true)->get()->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            'formActivities' => $activities->pluck('name', 'id'),
            'formSubactivities' => $activities->map(function (Activity $activity) {
                return ['subactivities' => $activity->subactivities->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
            'formCreateActivities' => $createActivities->pluck('name', 'id'),
            'formCreateSubactivities' => $createActivities->map(function (Activity $activity) {
                return ['subactivities' => $activity->subactivities->pluck('name', 'id'), 'id' => $activity->id];
            })->pluck('subactivities', 'id'),
            'groups' => NestedGroup::cacheForSelect(),
            'filter' => FilterScope::fromRequest(request()->input('filter', '')),
            'courses' => Course::pluck('name', 'id'),
            'regions' => Region::forSelect(),
            'subscriptions' => Subscription::pluck('name', 'id'),
            'countries' => Country::pluck('name', 'id'),
            'genders' => Gender::pluck('name', 'id'),
            'billKinds' => BillKind::forSelect(),
            'nationalities' => Nationality::pluck('name', 'id'),
            'members' => Member::ordered()->get()->map(fn($member) => ['id' => $member->id, 'name' => $member->fullname]),
            'links' => [
                'index' => route('member.index'),
                'create' => route('member.create'),
            ],
            'default_membership_filter' => [
                'group_ids' => [],
                'activity_ids' => [],
                'subactivity_ids' => []
            ],
            'boolean_filter' => [
                ['id' => true, 'name' => 'Ja'],
                ['id' => false, 'name' => 'Nein'],
            ],
            'default' => [
                'gender_id' => null,
                'salutation' => '',
                'nationality_id' => null,
                'firstname' => '',
                'lastname' => '',
                'address' => '',
                'further_address' => '',
                'zip' => '',
                'location' => '',
                'birthday' => '',
                'region_id' => null,
                'country_id' => Country::default(),
                'other_country' => '',
                'main_phone' => '',
                'mobile_phone' => '',
                'work_phone' => '',
                'children_phone' => '',
                'email' => '',
                'email_parents' => '',
                'fax' => '',
                'letter_address' => '',
                'bill_kind' => null,
                'subscription_id' => null,
                'has_nami' => false,
                'send_newspaper' => false,
                'joined_at' => now()->format('Y-m-d'),
                'comment' => '',
                'first_activity_id' => null,
                'first_subactivity_id' => null,
                'efz' => null,
                'ps_at' => null,
                'more_ps_at' => null,
                'without_education_at' => null,
                'recertified_at' => null,
                'without_efz_at' => null,
                'has_vk' => false,
                'has_svk' => false,
                'multiply_pv' => false,
                'multiply_more_pv' => false,
                'keepdata' => false,
                'bank_account' => [
                    'iban' => '',
                    'bic' => '',
                    'blz' => '',
                    'bank_name' => '',
                    'person' => '',
                    'account_number' => '',
                ]
            ]
        ];
    }
}
