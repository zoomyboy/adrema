<?php

namespace Tests\Feature\Member;

use App\Member\Actions\NamiDeleteMemberAction;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class NamiDeleteMemberActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testTheActionDeletesNamiMember(): void
    {
        app(MemberFake::class)->deletes(123, Carbon::parse('yesterday'));
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->inNami(123)->create();

        NamiDeleteMemberAction::dispatch(123);

        app(MemberFake::class)->assertDeleted(123, Carbon::parse('yesterday'));
    }
}
