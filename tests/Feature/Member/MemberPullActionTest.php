<?php

namespace Tests\Feature\Member;

use App\Actions\MemberPullAction;
use App\Member\Member;
use App\Member\Membership;
use App\Nationality;
use App\Region;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\CourseFake;
use Zoomyboy\LaravelNami\Fakes\MemberFake;
use Zoomyboy\LaravelNami\Fakes\MembershipFake;

class MemberPullActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testItUpdatesMemberships(): void
    {
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->inNami(60)->in('€ LeiterIn', 63))
            ->inNami(123)
            ->create();
        app(MemberFake::class)->shows(55, 123, [
            'vorname' => '::firstname::',
            'nachname' => '::lastname::',
            'beitragsartId' => 300,
            'geburtsDatum' => '2014-07-11 00:00:00',
            'gruppierungId' => 1000,
            'id' => 123,
            'eintrittsdatum' => '2020-11-17 00:00:00',
            'geschlechtId' => 303,
            'landId' => 302,
            'staatsangehoerigkeitId' => $member->nationality->nami_id,
            'zeitschriftenversand' => true,
            'strasse' => '::street',
            'plz' => '12346',
            'ort' => '::location::',
            'gruppierung' => 'testgroup',
            'version' => 40,
        ]);
        app(MembershipFake::class)
            ->fetches(123, [['id' => 60]])
            ->shows(123, [
                'id' => 60,
                'untergliederungId' => 2,
                'taetigkeitId' => 1,
                'gruppierungId' => 1400,
                'aktivVon' => '2022-02-03T00:00:00',
                'aktivBis' => '2022-02-03T00:00:01',
            ]);
        app(CourseFake::class)->fetches(123, []);
        $this->withoutExceptionHandling()->login()->loginNami();

        app(MemberPullAction::class)
            ->api(app(NamiSettings::class)->login())
            ->member(55, $member->nami_id)
            ->execute();

        $this->assertDatabaseMissing('memberships', [
            'member_id' => $member->id,
        ]);
    }

    public function testRegionIdIsSetToNull(): void
    {
        Nationality::factory()->inNami(1054)->create();
        Region::factory()->inNami(999)->name('nicht-de')->create(['is_null' => true]);
        app(MemberFake::class)->shows(55, 123, [
            'gruppierungId' => 55,
            'id' => 123,
            'regionId' => 999,
        ]);
        app(MembershipFake::class)->fetches(123, []);
        app(CourseFake::class)->fetches(123, []);
        $this->withoutExceptionHandling()->login()->loginNami();

        app(MemberPullAction::class)
            ->api(app(NamiSettings::class)->login())
            ->member(55, 123)
            ->execute();

        $this->assertDatabaseHas('members', [
            'region_id' => null,
        ]);
    }

    public function testItSetsNormalAttributes(): void
    {
        Nationality::factory()->inNami(1054)->create();
        $region = Region::factory()->inNami(999)->name('nicht-de')->create(['is_null' => false]);
        app(MemberFake::class)->shows(55, 123, [
            'regionId' => 999,
        ]);
        app(MembershipFake::class)->fetches(123, []);
        app(CourseFake::class)->fetches(123, []);
        $this->withoutExceptionHandling()->login()->loginNami();

        app(MemberPullAction::class)
            ->api(app(NamiSettings::class)->login())
            ->member(55, 123)
            ->execute();

        $this->assertDatabaseHas('members', [
            'region_id' => $region->id,
        ]);
    }
}
