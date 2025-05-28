<?php

namespace App\Prevention\Actions;

use App\Member\Member;
use App\Prevention\Data\PreventionData;
use App\Prevention\Mails\YearlyMail;
use App\Prevention\PreventionSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class YearlyRememberAction
{
    use AsAction;

    public string $commandSignature = 'prevention:remember-yearly';

    public function handle(): void
    {
        $settings = app(PreventionSettings::class);
        $expireDate = now()->addWeeks($settings->weeks);

        foreach (Member::get() as $member) {
            $noticePreventions = $member->preventions($expireDate)
                ->filter(fn($prevention) => $prevention->expiresAt($expireDate));

            if ($noticePreventions->count() === 0) {
                continue;
            }

            Mail::send($this->createMail($member, $noticePreventions));
        }

        foreach (Member::get() as $member) {
            $preventions = $member->preventions()
                ->filter(fn($prevention) => $prevention->expiresAt(now()));

            if ($preventions->count() === 0) {
                continue;
            }

            Mail::send($this->createMail($member, $preventions));
        }
    }

    /**
     * @param Collection<int, PreventionData> $preventions
     */
    protected function createMail(Member $member, Collection $preventions): YearlyMail
    {
        $body = app(PreventionSettings::class)->refresh()->formmail;
        return new YearlyMail($member, $body, $preventions);
    }
}
