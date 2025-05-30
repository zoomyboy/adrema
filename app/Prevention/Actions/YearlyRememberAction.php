<?php

namespace App\Prevention\Actions;

use App\Member\Member;
use App\Prevention\Data\PreventionData;
use App\Prevention\Mails\YearlyMail;
use App\Prevention\PreventionSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

        foreach ($settings->yearlyMemberFilter->getQuery()->get() as $member) {
            // @todo add this check to FilterScope
            if ($member->getMailRecipient() === null) {
                continue;
            }

            $noticePreventions = $member->preventions($expireDate)
                ->filter(fn($prevention) => $prevention->expiresAt($expireDate))
                ->filter(fn($p) => $p->appliesToSettings($settings));

            if ($noticePreventions->count() === 0) {
                continue;
            }

            Mail::send($this->createMail($member, $noticePreventions));
        }

        foreach ($settings->yearlyMemberFilter->getQuery()->get() as $member) {
            // @todo add this check to FilterScope
            if ($member->getMailRecipient() === null) {
                continue;
            }

            $preventions = $member->preventions()
                ->filter(fn($prevention) => $prevention->expiresAt(now()))
                ->filter(fn($p) => $p->appliesToSettings($settings));

            if ($preventions->count() === 0) {
                continue;
            }

            Cache::remember(
                'prevention-' . $member->id,
                (int) now()->diffInSeconds(now()->addWeeks($settings->freshRememberInterval)),
                function () use ($member, $preventions) {
                    Mail::send($this->createMail($member, $preventions));
                    return 0;
                }
            );
        }
    }

    /**
     * @param Collection<int, PreventionData> $preventions
     */
    protected function createMail(Member $member, Collection $preventions): YearlyMail
    {
        $body = app(PreventionSettings::class)->refresh()->yearlymail;
        return new YearlyMail($member, $body, $preventions);
    }
}
