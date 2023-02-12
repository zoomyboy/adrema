<?php

namespace App\Actions;

use App\Member\Member;
use App\Nami\Api\CoursesOfAction;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;

class PullCoursesAction
{
    use AsAction;

    public function handle(Member $member): void
    {
        if (!$member->hasNami) {
            return;
        }

        InsertCoursesAction::run($member, CoursesOfAction::run($this->api(), $member->nami_id));
    }

    private function api(): Api
    {
        return app(NamiSettings::class)->login();
    }
}
