<?php

namespace App\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zoomyboy\LaravelNami\Nami;

class UpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $memberId;
    public $member;

    public function __construct(Member $member)
    {
        $this->memberId = $member->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->member = Member::find($this->memberId);

        Nami::putMember([
            'firstname' => $this->member->firstname,
            'lastname' => $this->member->lastname,
            'nickname' => $this->member->nickname,
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
            'gender_id' => optional($this->member)->nami_id,
            'confession_id' => optional($this->member->confession)->nami_id,
            'region_id' => optional($this->member->region)->nami_id,
            'country_id' => $this->member->country->nami_id,
            'fee_id' => optional($this->member->fee)->nami_id,
            'nationality_id' => $this->member->nationality->nami_id,
            'id' => $this->member->nami_id,
            'group_id' => $this->member->group->nami_id,
        ]);
    }
}
