<?php

namespace App\Member;

use App\Lib\Data\JobMiddleware\SendsMessages;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteJob
{
    use AsAction;

    public function handle(int $namiId)
    {
        app(NamiSettings::class)->login()->deleteMember($namiId);
    }
}
