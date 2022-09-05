<?php

namespace App\Member;

use App\Setting\NamiSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $memberId;
    public Member $member;

    public function __construct(Member $member)
    {
        $this->memberId = $member->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NamiSettings $setting)
    {
        $this->member = Member::find($this->memberId);

        if (!$this->member->hasNami) {
            return;
        }

        $setting->login()->deleteMember($this->member->nami_id);

        Member::withoutEvents(function () {
            $this->member->update(['nami_id' => null]);
        });
    }
}
