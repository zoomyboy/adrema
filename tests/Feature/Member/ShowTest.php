<?php

namespace Tests\Feature\Member;

use App\Fee;
use App\Gender;
use App\Group;
use App\Member\Member;
use App\Member\Membership;
use App\Nationality;
use App\Payment\Payment;
use App\Payment\Subscription;
use App\Region;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2006-01-01 15:00:00'));
    }

    public function testItShowsSingleMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Jungpfadfinder', 88)->state(['created_at' => '2022-11-19 05:00:00']))
            ->has(Payment::factory()->notPaid()->nr('2019')->subscription('Free', 1050))
            ->for(Gender::factory()->name('Herr'))
            ->for(Region::factory()->name('NRW'))
            ->create([
                'birthday' => '1991-04-20',
                'address' => 'Itterstr 3',
                'zip' => '42719',
                'location' => 'Solingen',
                'firstname' => 'Max',
                'other_country' => 'other',
                'main_phone' => '+49 212 1266775',
                'mobile_phone' => '+49 212 1266776',
                'work_phone' => '+49 212 1266777',
                'children_phone' => '+49 212 1266778',
                'email' => 'a@b.de',
                'email_parents' => 'b@c.de',
                'fax' => '+49 212 1255674',
            ]);

        $response = $this->get("/member/{$member->id}");

        $this->assertInertiaHas([
            'birthday_human' => '20.04.1991',
            'age' => 14,
            'firstname' => 'Max',
            'gender_name' => 'Herr',
            'full_address' => 'Itterstr 3, 42719 Solingen',
            'region' => ['name' => 'NRW'],
            'other_country' => 'other',
            'main_phone' => '+49 212 1266775',
            'mobile_phone' => '+49 212 1266776',
            'work_phone' => '+49 212 1266777',
            'children_phone' => '+49 212 1266778',
            'email' => 'a@b.de',
            'email_parents' => 'b@c.de',
            'fax' => '+49 212 1255674',
        ], $response, 'data');
        $this->assertInertiaHas([
            'activity_name' => '€ LeiterIn',
            'id' => $member->memberships->first()->id,
            'human_date' => '19.11.2022',
         ], $response, 'data.memberships.0');
        $this->assertInertiaHas([
            'subscription' => [
                'name' => 'Free',
                'id' => $member->payments->first()->subscription->id,
                'amount' => 1050,
            ],
            'status_name' => 'Nicht bezahlt',
            'nr' => '2019',
         ], $response, 'data.payments.0');
    }

    public function testItShowsMinimalSingleMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->for(Group::factory())
            ->for(Nationality::factory()->name('deutsch'))
            ->for(Subscription::factory()->for(Fee::factory()))
            ->create(['firstname' => 'Max']);

        $response = $this->get("/member/{$member->id}");

        $this->assertInertiaHas([
            'region' => ['name' => '-- kein --'],
            'nationality' => ['name' => '-- kein --'],
            'gender_name' => 'keine Angabe',
            'nationality' => [
                'name' => 'deutsch',
            ],
        ], $response, 'data');
    }
}
