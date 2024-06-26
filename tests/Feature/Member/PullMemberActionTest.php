<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Country;
use App\Fee;
use App\Gender;
use App\Group;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class PullMemberActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Subscription::factory()->name('test')->forFee(300)->create();
        Gender::factory()->inNami(303)->create();
        Country::factory()->inNami(302)->create();
        Nationality::factory()->inNami(1054)->create();
        $this->loginNami();
    }

    public function testFetchNormalMember(): void
    {
        app(MemberFake::class)->shows(1000, 1001, [
            'vorname' => '::firstname::',
            'nachname' => '::lastname::',
            'beitragsartId' => 300,
            'geburtsDatum' => '2014-07-11 00:00:00',
            'gruppierungId' => 1000,
            'geschlechtId' => 303,
            'id' => 1001,
            'eintrittsdatum' => '2020-11-17 00:00:00',
            'landId' => 302,
            'staatsangehoerigkeitId' => 1054,
            'zeitschriftenversand' => true,
            'strasse' => '::street::',
            'plz' => '12346',
            'ort' => '::location::',
            'version' => 40,
            'gruppierung' => 'SG Wald',
            'mitgliedsNummer' => 53,
        ]);

        $member = app(PullMemberAction::class)->handle(1000, 1001);

        $this->assertDatabaseHas('members', [
            'firstname' => '::firstname::',
            'lastname' => '::lastname::',
            'subscription_id' => Subscription::firstWhere('name', 'test')->id,
            'birthday' => '2014-07-11',
            'group_id' => Group::nami(1000)->id,
            'gender_id' => Gender::nami(303)->id,
            'nami_id' => 1001,
            'joined_at' => '2020-11-17',
            'country_id' => Country::nami(302)->id,
            'nationality_id' => Nationality::nami(1054)->id,
            'send_newspaper' => 1,
            'address' => '::street::',
            'zip' => '12346',
            'location' => '::location::',
            'version' => '40',
            'mitgliedsnr' => 53,
        ]);

        $this->assertDatabaseHas('groups', [
            'name' => 'SG Wald',
            'nami_id' => 1000,
            'inner_name' => 'SG Wald',
        ]);
        $this->assertEquals(1001, $member->nami_id);
    }

    public function testRegionIdIsSetToNull(): void
    {
        Region::factory()->inNami(999)->name('nicht-de')->create(['is_null' => true]);
        app(MemberFake::class)->shows(1000, 1001, [
            'regionId' => 999,
        ]);

        app(PullMemberAction::class)->handle(1000, 1001);

        $this->assertDatabaseHas('members', [
            'region_id' => null,
        ]);
    }

    public function testItSetsFirstSubscriptionFromFee(): void
    {
        Region::factory()->inNami(999)->name('nicht-de')->create(['is_null' => true]);
        $should = Subscription::factory()->forFee(55)->create();
        app(MemberFake::class)->shows(1000, 1001, [
            'beitragsartId' => 55,
        ]);

        app(PullMemberAction::class)->handle(1000, 1001);

        $this->assertDatabaseHas('members', [
            'subscription_id' => $should->id,
        ]);
    }

    public function testItCreatesSubscriptionOnTheFly(): void
    {
        Region::factory()->inNami(999)->name('nicht-de')->create(['is_null' => true]);
        app(MemberFake::class)->shows(1000, 1001, [
            'beitragsartId' => 55,
            'beitragsart' => 'Lala',
        ]);

        app(PullMemberAction::class)->handle(1000, 1001);

        $fee = Fee::where('nami_id', 55)->firstOrFail();
        $subscription = Subscription::where('fee_id', $fee->id)->firstOrFail();
        $this->assertDatabaseHas('subscriptions', [
            'fee_id' => $fee->id,
            'name' => 'Lala',
        ]);
        $this->assertDatabaseHas('subscription_children', [
            'name' => 'Lala',
            'amount' => 1000,
            'parent_id' => $subscription->id,
        ]);
        $this->assertDatabaseHas('members', [
            'subscription_id' => $subscription->id,
        ]);
    }

    public function testItPullsMemberWithNoSubscription(): void
    {
        Region::factory()->inNami(999)->name('nicht-de')->create(['is_null' => true]);
        app(MemberFake::class)->shows(1000, 1001, [
            'beitragsartId' => null,
            'beitragsart' => null,
        ]);

        app(PullMemberAction::class)->handle(1000, 1001);

        $this->assertDatabaseHas('members', [
            'subscription_id' => null,
        ]);
    }
}
