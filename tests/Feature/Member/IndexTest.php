<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItGetsMembers(): void
    {
        $backend = app(FakeBackend::class)
            ->fakeMember([
                'vorname' => '::firstname::',
                'nachname' => '::lastname::',
                'beitragsartId' => 300,
                'geburtsDatum' => '2014-07-11 00:00:00',
                'gruppierungId' => 1000,
                'id' => 411,
                'eintrittsdatum' => '2020-11-17 00:00:00',
                'geschlechtId' => 303,
                'landId' => 302,
                'staatsangehoerigkeitId' => 291,
                'zeitschriftenversand' => true,
                'strasse' => '::street',
                'plz' => '12346',
                'ort' => '::location::',
                'gruppierung' => '::group::',
                'version' => 40,
            ]);
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        Member::factory()->defaults()->has(CourseMember::factory()->for(Course::factory()), 'courses')->create(['firstname' => '::firstname::']);

        $response = $this->get('/member');

        $this->assertComponent('member/VIndex', $response);
        $this->assertInertiaHas('::firstname::', $response, 'data.data.0.firstname');
    }

    public function testItShowsEfzForEfzMembership(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->for(Subactivity::factory()->ageGroup())->for(Activity::factory()->state(['has_efz' => true])))
            ->create(['lastname' => 'A']);
        Member::factory()
            ->defaults()
            ->has(Membership::factory()->for(Subactivity::factory()->ageGroup())->for(Activity::factory()->state(['has_efz' => false])))
            ->create(['lastname' => 'B']);
        Member::factory()
            ->defaults()
            ->create(['lastname' => 'C']);

        $response = $this->get('/member');

        $this->assertInertiaHas(url("/member/{$member->id}/efz"), $response, 'data.data.0.efz_link');
        $this->assertInertiaHas(null, $response, 'data.data.1.efz_link');
        $this->assertInertiaHas(null, $response, 'data.data.2.efz_link');
        $this->assertInertiaHas(true, $response, 'data.data.0.is_leader');
        $this->assertInertiaHas(false, $response, 'data.data.1.is_leader');
        $this->assertInertiaHas(false, $response, 'data.data.2.is_leader');
    }

    public function testItShowsAgeGroupIcon(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->for(Subactivity::factory()->ageGroup()->name('Wölfling'))->for(Activity::factory()->state(['has_efz' => false])))
            ->create();

        $response = $this->get('/member');

        $this->assertInertiaHas('woelfling', $response, 'data.data.0.age_group_icon');
    }

    public function testItShowsActivitiesAndSubactivities(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('SG Nahost')->ageGroup()->filterable())->name('Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get('/member');

        $this->assertInertiaHas('SG Nahost', $response, "subactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('SG Nahost', $response, "filterSubactivities.{$subactivity->id}");
        $this->assertInertiaHas('Mitglied', $response, "activities.{$activity->id}");
    }
}
