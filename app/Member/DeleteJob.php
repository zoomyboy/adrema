<?php

namespace App\Member;

use App\Confession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zoomyboy\LaravelNami\Nami;
use Zoomyboy\LaravelNami\NamiUser;

class DeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $memberId;
    public Member $member;
    public NamiUser $user;

    public function __construct(Member $member, NamiUser $user)
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

        if (!$this->member->hasNami) {
            return;
        }

        Nami::login($this->user->mglnr)->deleteMember($this->member->nami_id);

        Member::withoutEvents(function() {
            $this->member->update(['nami_id' => null]);
        });
    }
}
