<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use App\Member\Membership;
use App\Payment\Payment;
use App\Subactivity;
use Carbon\Carbon;
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
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->has(CourseMember::factory()->for(Course::factory()), 'courses')->create(['firstname' => '::firstname::']);

        $response = $this->get('/member');

        $this->assertComponent('member/VIndex', $response);
        $this->assertInertiaHas('::firstname::', $response, 'data.data.0.firstname');
    }

    public function testItShowsEfzForEfzMembership(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 455, 'Pfadfinder', 15))
            ->create(['lastname' => 'A']);
        Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 456, 'Pfadfinder', 16))
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
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Wölfling', 12))
            ->create();

        $response = $this->get('/member');

        $this->assertInertiaHas('woelfling', $response, 'data.data.0.age_group_icon');
    }

    public function testItShowsActivitiesAndSubactivities(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get('/member');

        $this->assertInertiaHas('Biber', $response, "subactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('Biber', $response, "filterSubactivities.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "activities.{$activity->id}");
    }

    public function testItShowsActivityAndSubactivityNamesOfMember(): void
    {
        Carbon::setTestNow(Carbon::parse('2022-11-02 03:00:00'));
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 122, 'Wölfling', 234))
            ->create();

        $response = $this->get('/member');

        $this->assertInertiaHas([
            'activity_id' => $member->memberships->first()->activity_id,
            'subactivity_id' => $member->memberships->first()->subactivity_id,
            'activity_name' => '€ Mitglied',
            'subactivity_name' => 'Wölfling',
            'human_date' => '02.11.2022',
            'id' => $member->memberships->first()->id,
        ], $response, 'data.data.0.memberships.0');
    }

    public function testItReturnsPayments(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->has(Payment::factory()->notPaid()->nr('2019')->subscription('Free', 1050))
            ->defaults()->create();

        $response = $this->get('/member');

        $this->assertInertiaHas([
            'subscription' => [
                'name' => 'Free',
                'id' => $member->payments->first()->subscription->id,
                'amount' => 1050,
            ],
            'subscription_id' => $member->payments->first()->subscription->id,
            'status_name' => 'Nicht bezahlt',
            'nr' => '2019',
         ], $response, 'data.data.0.payments.0');
        $this->assertInertiaHas([
            'id' => $member->subscription->id,
            'name' => $member->subscription->name,
         ], $response, 'data.data.0.subscription');
    }
}
