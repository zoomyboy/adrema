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

    public int $namiId;

    public function __construct(int $namiId)
    {
        $this->namiId = $namiId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NamiSettings $setting)
    {
        $setting->login()->deleteMember($this->namiId);
    }
}
