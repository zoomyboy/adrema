<?php

namespace App\Member\Actions;

use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;

class NamiDeleteMemberAction
{
    use AsAction;

    public function handle(int $namiId): void
    {
        app(NamiSettings::class)->login()->deleteMember($namiId);
    }
}
