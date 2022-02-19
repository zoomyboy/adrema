<?php

namespace App\Member;

use App\Activity;
use App\Confession;
use App\Group;
use App\Subactivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zoomyboy\LaravelNami\Nami;

class CreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $memberId;
    public Member $member;

    public function __construct(Member $member)
    {
        $this->memberId = $member->id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->member = Member::find($this->memberId);

        if ($this->member->hasNami) {
            return;
        }

        $response = $this->user->api()->putMember([
            'firstname' => $this->member->firstname,
            'lastname' => $this->member->lastname,
            'joined_at' => $this->member->joined_at,
            'birthday' => $this->member->birthday,
            'send_newspaper' => $this->member->send_newspaper,
            'address' => $this->member->address,
            'zip' => $this->member->zip,
            'location' => $this->member->location,
            'nickname' => $this->member->nickname,
            'other_country' => $this->member->other_country,
            'further_address' => $this->member->further_address,
            'main_phone' => $this->member->main_phone,
            'mobile_phone' => $this->member->mobile_phone,
            'work_phone' => $this->member->work_phone,
            'fax' => $this->member->fax,
            'email' => $this->member->email,
            'email_parents' => $this->member->email_parents,
            'gender_id' => optional($this->member->gender)->nami_id,
            'confession_id' => $this->member->confession ? $this->member->confession->nami_id : Confession::firstWhere('is_null', true)->id,
            'region_id' => optional($this->member->region)->nami_id,
            'country_id' => $this->member->country->nami_id,
            'fee_id' => $this->member->getNamiFeeId(),
            'nationality_id' => $this->member->nationality->nami_id,
            'group_id' => $this->member->group->nami_id,
            'first_activity_id' => $this->member->firstActivity->nami_id,
            'first_subactivity_id' => $this->member->firstSubactivity->nami_id,
        ]);
        Member::withoutEvents(function() use ($response) {
            $version = Nami::login($this->user->mglnr)->member($this->member->group->nami_id, $response['id'])['version'];
            $this->member->update(['version' => $version, 'nami_id' => $response['id']]);
        });

        $memberships = $this->member->getNamiMemberships($this->user->api());
        foreach ($memberships as $membership) {
            $this->member->memberships()->create([
                'activity_id' => Activity::nami($membership['activity_id'])->id,
                'subactivity_id' => $membership['subactivity_id']
                    ? Subactivity::nami($membership['subactivity_id'])->id
                    : null,
                'group_id' => Group::nami($membership['group_id'])->id,
                'nami_id' => $membership['id'],
                'created_at' => $membership['starts_at'],
            ]);
        }

    }
}
