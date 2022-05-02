<?php

namespace App\Member;

use App\Actions\MemberPullAction;
use App\Confession;
use App\Setting\NamiSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $memberId;

    public function __construct(Member $member)
    {
        $this->memberId = $member->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NamiSettings $settings)
    {
        $member = Member::findOrFail($this->memberId);
        if ($member->hasNami) {
            return;
        }
        $api = $settings->login();
        $response = $api->putMember([
            'firstname' => $member->firstname,
            'lastname' => $member->lastname,
            'joined_at' => $member->joined_at,
            'birthday' => $member->birthday,
            'send_newspaper' => $member->send_newspaper,
            'address' => $member->address,
            'zip' => $member->zip,
            'location' => $member->location,
            'nickname' => $member->nickname,
            'other_country' => $member->other_country,
            'further_address' => $member->further_address,
            'main_phone' => $member->main_phone,
            'mobile_phone' => $member->mobile_phone,
            'work_phone' => $member->work_phone,
            'fax' => $member->fax,
            'email' => $member->email,
            'email_parents' => $member->email_parents,
            'gender_id' => optional($member->gender)->nami_id,
            'confession_id' => $member->confession ? $member->confession->nami_id : Confession::firstWhere('is_null', true)->id,
            'region_id' => optional($member->region)->nami_id,
            'country_id' => $member->country->nami_id,
            'fee_id' => $member->getNamiFeeId(),
            'nationality_id' => $member->nationality->nami_id,
            'group_id' => $member->group->nami_id,
            'first_activity_id' => $member->firstActivity->nami_id,
            'first_subactivity_id' => $member->firstSubactivity->nami_id,
        ]);
        Member::withoutEvents(function () use ($response, $member, $api) {
            $member->update(['nami_id' => $response['id']]);
            app(MemberPullAction::class)->api($api)->member($member->group->nami_id, $member->nami_id)->execute();
        });
    }
}
