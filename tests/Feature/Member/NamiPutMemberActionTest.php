<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
use App\Country;
use App\Fee;
use App\Gender;
use App\Group;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class NamiPutMemberActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testItPutsAMember(): void
    {
        Fee::factory()->create();
        $this->stubIo(PullMemberAction::class, fn ($mock) => $mock);
        $this->stubIo(PullMembershipsAction::class, fn ($mock) => $mock);
        $this->withoutExceptionHandling()->login()->loginNami();
        $country = Country::factory()->create();
        $gender = Gender::factory()->create();
        $region = Region::factory()->create();
        $nationality = Nationality::factory()->inNami(565)->create();
        $subscription = Subscription::factory()->create();
        $group = Group::factory()->inNami(55)->create();
        $confession = Confession::factory()->inNami(567)->create(['is_null' => true]);
        app(MemberFake::class)->createsSuccessfully(55, 993);
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber')->inNami(55))->name('Leiter')->inNami(6)->create();
        $subactivity = $activity->subactivities->first();

        $member = Member::factory()
            ->for($country)
            ->for($subscription)
            ->for($region)
            ->for($nationality)
            ->for($gender)
            ->for($group)
            ->emailBillKind()
            ->create();

        NamiPutMemberAction::run($member, $activity, $subactivity);

        app(MemberFake::class)->assertCreated(55, [
            'ersteTaetigkeitId' => 6,
            'ersteUntergliederungId' => 55,
            'konfessionId' => 567,
        ]);
        $this->assertDatabaseHas('members', [
            'nami_id' => 993,
        ]);
        Phake::verify(app(PullMemberAction::class))->handle(55, 993);
        Phake::verify(app(PullMembershipsAction::class))->handle($member);
    }
}
