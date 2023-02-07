<?php

namespace Tests\Feature\Initialize;

use App\Actions\PullCoursesAction;
use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Initialize\InitializeMembers;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Data\MemberEntry;
use Zoomyboy\LaravelNami\Fakes\SearchFake;

class InitializeMembersTest extends TestCase
{
    use DatabaseTransactions;

    public function testItInitializesMembers(): void
    {
        $this->loginNami();
        $api = app(NamiSettings::class)->login();
        app(SearchFake::class)->fetches(1, 0, [
            MemberEntry::factory()->toMember(['groupId' => 100, 'id' => 20]),
        ]);
        PullMemberAction::shouldRun()->once()->with(100, 20);
        PullMembershipsAction::shouldRun()->once();
        PullCoursesAction::shouldRun()->once();

        app(InitializeMembers::class)->handle($api);
    }
}
