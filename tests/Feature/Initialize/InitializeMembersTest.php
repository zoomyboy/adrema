<?php

namespace Tests\Feature\Initialize;

use App\Initialize\InitializeMembers;
use App\Nami\Api\FullMemberAction;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
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
        app(SearchFake::class)->fetches(1, 0, 100, [
            MemberEntry::toFactory()->toMember(['groupId' => 100, 'id' => 20]),
        ]);
        FullMemberAction::partialMock()->shouldReceive('configureJob')->once();
        FullMemberAction::partialMock()->shouldReceive('handle')->once();

        app(InitializeMembers::class)->handle($api);
    }

    public function testFetchesMembersViaCommandLine(): void
    {
        $this->loginNami();
        app(SearchFake::class)->fetches(1, 0, 100, [
            MemberEntry::toFactory()->toMember(['groupId' => 100, 'id' => 20]),
        ]);
        FullMemberAction::partialMock()->shouldReceive('configureJob')->once();
        FullMemberAction::partialMock()->shouldReceive('handle')->once();

        Artisan::call('member:pull');
    }
}
