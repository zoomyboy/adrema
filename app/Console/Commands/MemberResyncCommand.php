<?php

namespace App\Console\Commands;

use App\Actions\MemberPullAction;
use App\Setting\NamiSettings;
use Illuminate\Console\Command;
use Zoomyboy\LaravelNami\Member as NamiMember;

class MemberResyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:resync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NamiSettings $settings)
    {
        $api = $settings->login();

        $api->search([])->each(
            fn (NamiMember $member) => app(MemberPullAction::class)->api($api)->member($member->group_id, $member->id)->execute()
        );

        return 0;
    }
}
