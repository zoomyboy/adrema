<?php

namespace App\Member;

use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAction
{
    use AsAction;

    public function handle(int $namiId): void
    {
        app(NamiSettings::class)->login()->deleteMember($namiId);
    }
}
