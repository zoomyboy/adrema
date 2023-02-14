<?php

namespace Tests\Feature\Initialize;

use App\Initialize\InitializeMembers;
use App\Member\Member;
use App\Nami\Api\CoursesOfAction;
use App\Nami\Api\MembershipsOfAction;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Data\MemberEntry;
use Zoomyboy\LaravelNami\Fakes\MemberFake;
use Zoomyboy\LaravelNami\Fakes\SearchFake;

class InitializeMembersTest extends TestCase
{
    use DatabaseTransactions;

    public function testItInitializesMembers(): void
    {
        $this->loginNami();
        $member = Member::factory()->defaults()->create();
        $api = app(NamiSettings::class)->login();
        app(SearchFake::class)->fetches(1, 0, [
            MemberEntry::factory()->toMember(['groupId' => 100, 'id' => 20]),
        ]);
        app(MemberFake::class)->shows(100, 20);
        MembershipsOfAction::shouldRun()->once()->withArgs(fn ($api, $id) => 20 === $id)->andReturn(collect([]));
        CoursesOfAction::shouldRun()->once()->withArgs(fn ($api, $id) => 20 === $id)->andReturn(collect([]));

        app(InitializeMembers::class)->handle($api);
    }

    public function testFetchesMembersViaCommandLine(): void
    {
        $this->loginNami();
        $api = app(NamiSettings::class)->login();

        app(SearchFake::class)->fetches(1, 0, [
            MemberEntry::factory()->toMember(['groupId' => 100, 'id' => 20]),
        ]);
        app(MemberFake::class)->shows(100, 20);
        MembershipsOfAction::shouldRun()->once()->withArgs(fn ($api, $id) => 20 === $id)->andReturn(collect([]));
        CoursesOfAction::shouldRun()->once()->withArgs(fn ($api, $id) => 20 === $id)->andReturn(collect([]));

        Artisan::call('member:pull');
    }
}
