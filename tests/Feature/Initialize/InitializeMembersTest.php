<?php

namespace Tests\Feature\Initialize;

use App\Actions\PullCoursesAction;
use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Initialize\InitializeMembers;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Data\MemberEntry;
use Zoomyboy\LaravelNami\Exceptions\MemberDataCorruptedException;
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
        PullMemberAction::shouldRun()->once()->with(100, 20)->andReturn($member);
        PullMembershipsAction::shouldRun()->once()->with($member);
        PullCoursesAction::shouldRun()->once()->with($member);

        app(InitializeMembers::class)->handle($api);
    }

    public function testFetchesMembersWhenJoinedAtDateIsNull(): void
    {
        $this->loginNami();
        $api = app(NamiSettings::class)->login();
        app(SearchFake::class)->fetches(1, 0, [
            MemberEntry::factory()->toMember(['groupId' => 100, 'id' => 20]),
            MemberEntry::factory()->toMember(['groupId' => 100, 'id' => 21]),
        ]);
        PullMemberAction::shouldRun()->once()->with(100, 20)->andThrow(MemberDataCorruptedException::class, []);
        PullMemberAction::shouldRun()->once()->with(100, 21);
        PullMembershipsAction::shouldRun()->once();
        PullCoursesAction::shouldRun()->once();

        app(InitializeMembers::class)->handle($api);
    }

    public function testFetchesMembersViaCommandLine(): void
    {
        $this->loginNami();
        $api = app(NamiSettings::class)->login();

        app(SearchFake::class)->fetches(1, 0, [
            MemberEntry::factory()->toMember(['groupId' => 100, 'id' => 20]),
        ]);
        PullMemberAction::shouldRun()->once()->with(100, 20);
        PullMembershipsAction::shouldRun()->once();
        PullCoursesAction::shouldRun()->once();

        Artisan::call('member:pull');
    }
}
